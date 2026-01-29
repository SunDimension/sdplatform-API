<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class PollDeployments extends Command
{
    protected $signature = 'deploy:poll';
    protected $description = 'Poll the hub for pending deployment requests and execute them';

    protected string $basePath = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';
    protected string $branch   = 'db-sync-v2';
    protected string $hubUrl = 'https://sync.hamirglobal.com/api/deploy/pending';

    protected string $deployToken;

    public function __construct()
    {
        parent::__construct();
        $this->deployToken = env('DEPLOY_TOKEN');
    }

    public function handle()
    {
        $this->info('Polling hub for pending deployments...');

        try {
            $response = Http::withHeaders([
                'X-DEPLOY-TOKEN' => $this->deployToken,
            ])->timeout(60)->post($this->hubUrl);

            if (! $response->successful()) {
                $this->error('Failed to fetch deployments: ' . $response->status());
                return 1;
            }

            $deployments = $response->json()['deployments'] ?? [];

            if (empty($deployments)) {
                $this->info('No pending deployments.');
                return 0;
            }

            foreach ($deployments as $deployment) {
                $this->info("Executing deployment request #{$deployment['id']}...");

                $lockFile = storage_path("deploy_{$deployment['id']}.lock");

                if (file_exists($lockFile)) {
                    $this->warn("Deployment #{$deployment['id']} already running. Skipping.");
                    continue;
                }

                touch($lockFile);

                try {
                    $this->runDeployment();
                    $this->notifyHubCompleted($deployment['id'], 'completed', 'Deployment successful.');
                    $this->info("Deployment #{$deployment['id']} completed.");
                } catch (\Throwable $e) {
                    $this->notifyHubCompleted($deployment['id'], 'failed', $e->getMessage());
                    $this->error("Deployment #{$deployment['id']} failed: " . $e->getMessage());
                } finally {
                    if (file_exists($lockFile)) {
                        unlink($lockFile);
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->error('Polling error: ' . $e->getMessage());
            Log::error('PollDeployments error', ['error' => $e->getMessage()]);
        }

        return 0;
    }

    protected function runDeployment()
    {
        $version = now()->format('Ymd_His');
        $releasePath = "{$this->basePath}\\releases\\{$version}";
        $sharedPath  = "{$this->basePath}\\shared";
        $currentPath = "{$this->basePath}\\current";

        // Ensure releases directory exists
        if (!is_dir("{$this->basePath}\\releases")) {
            mkdir("{$this->basePath}\\releases", 0755, true);
        }

        // Create release directory
        if (!is_dir($releasePath)) {
            mkdir($releasePath, 0755, true);
        }

        // Get the repository URL from current directory
        $repoUrl = $this->getGitRepoUrl();

        $this->info("Cloning from: {$repoUrl}");
        $this->info("Branch: {$this->branch}");

        // Commands with proper Windows handling
        $commands = [
            // Clone repository
            [
                'command' => "git clone -b {$this->branch} {$repoUrl} {$releasePath}",
                'cwd' => $this->basePath,
                'description' => 'Cloning repository'
            ],
            // Remove storage directory created by git (so we can create symlink)
            [
                'command' => "powershell -Command \"if (Test-Path '{$releasePath}\\storage') { Remove-Item -Path '{$releasePath}\\storage' -Recurse -Force }\"",
                'cwd' => $releasePath,
                'description' => 'Removing cloned storage directory'
            ],
            // Create .env symlink
            [
                'command' => "mklink {$releasePath}\\.env {$sharedPath}\\.env",
                'cwd' => $releasePath,
                'description' => 'Creating .env symlink'
            ],
            // Create storage symlink
            [
                'command' => "mklink /J {$releasePath}\\storage {$sharedPath}\\storage",
                'cwd' => $releasePath,
                'description' => 'Creating storage symlink'
            ],
            // Composer install
            [
                'command' => 'composer install --no-dev --optimize-autoloader --no-interaction',
                'cwd' => $releasePath,
                'description' => 'Installing composer dependencies'
            ],
            // Run migrations
            [
                'command' => 'php artisan migrate --force',
                'cwd' => $releasePath,
                'description' => 'Running migrations'
            ],
            // Optimize application
            [
                'command' => 'php artisan optimize',
                'cwd' => $releasePath,
                'description' => 'Optimizing application'
            ],
        ];

        foreach ($commands as $item) {
            $this->info("→ {$item['description']}...");

            $process = Process::fromShellCommandline(
                $item['command'],
                $item['cwd'],
                null,
                null,
                600
            );

            $process->run();

            $output = trim($process->getOutput());
            $errorOutput = trim($process->getErrorOutput());

            if (!$process->isSuccessful()) {
                $fullOutput = "STDOUT:\n{$output}\n\nSTDERR:\n{$errorOutput}";

                Log::error("Deployment step failed", [
                    'step' => $item['description'],
                    'command' => $item['command'],
                    'cwd' => $item['cwd'],
                    'exit_code' => $process->getExitCode(),
                    'output' => $fullOutput,
                ]);

                throw new \RuntimeException(
                    "Command failed: {$item['command']}\n" .
                    "Working directory: {$item['cwd']}\n" .
                    "Exit code: {$process->getExitCode()}\n" .
                    "Output: {$fullOutput}"
                );
            }

            if ($output || $errorOutput) {
                Log::info("Deployment step completed", [
                    'step' => $item['description'],
                    'output' => $output ?: $errorOutput,
                ]);
            }
        }

        // Switch to new release (atomic operation)
        $this->switchRelease($currentPath, $releasePath);

        $this->info("✓ Deployment completed successfully: {$version}");
    }

    protected function switchRelease(string $currentPath, string $releasePath)
    {
        // Remove old symlink/junction using PowerShell (more reliable on Windows)
        if (file_exists($currentPath)) {
            $this->info("Removing existing 'current' link...");
            
            $removeProcess = Process::fromShellCommandline(
                "powershell -Command \"if (Test-Path '{$currentPath}') { " .
                "(Get-Item '{$currentPath}').Delete() }\"",
                dirname($currentPath)
            );

            $removeProcess->run();

            if (!$removeProcess->isSuccessful()) {
                $this->warn("Warning: Could not remove existing 'current' link. Trying alternative method...");
                
                // Alternative: use rmdir for junctions (no /S flag for junctions!)
                $altRemove = Process::fromShellCommandline(
                    "cmd /c rmdir \"{$currentPath}\"",
                    dirname($currentPath)
                );
                
                $altRemove->run();
                
                if (!$altRemove->isSuccessful()) {
                    throw new \RuntimeException(
                        "Failed to remove existing 'current' link.\n" .
                        "Error: " . $removeProcess->getErrorOutput() . "\n" .
                        "Alt Error: " . $altRemove->getErrorOutput()
                    );
                }
            }
            
            $this->info("✓ Removed existing 'current' link");
        }

        // Create new junction
        $createProcess = Process::fromShellCommandline(
            "mklink /J \"{$currentPath}\" \"{$releasePath}\"",
            dirname($currentPath)
        );

        $createProcess->run();

        if (!$createProcess->isSuccessful()) {
            throw new \RuntimeException(
                "Failed to create symlink to new release.\n" .
                "Command: mklink /J {$currentPath} {$releasePath}\n" .
                "Output: " . $createProcess->getErrorOutput()
            );
        }

        $this->info("✓ Switched to new release");
    }

    protected function getGitRepoUrl(): string
    {
        // Try to get from current directory
        $currentGitDir = "{$this->basePath}\\current\\.git";

        if (is_dir($currentGitDir)) {
            $process = Process::fromShellCommandline(
                'git config --get remote.origin.url',
                "{$this->basePath}\\current"
            );

            $process->run();

            if ($process->isSuccessful()) {
                return trim($process->getOutput());
            }
        }

        // Fallback - you should set this to your actual repo URL
        throw new \RuntimeException(
            'Could not determine git repository URL. ' .
            'Please set it manually in the command or ensure current directory has a .git folder.'
        );
    }

    protected function notifyHubCompleted(int $deploymentId, string $status, string $message)
    {
        try {
            Http::withHeaders([
                'X-DEPLOY-TOKEN' => $this->deployToken,
            ])->timeout(30)->post('https://sync.hamirglobal.com/api/deploy/complete', [
                'deployment_id' => $deploymentId,
                'status' => $status,
                'response' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to notify hub', [
                'deployment_id' => $deploymentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}