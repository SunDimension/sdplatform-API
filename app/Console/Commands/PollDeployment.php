<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class PollDeployment extends Command
{
    protected $signature = 'deploy:poll';
    protected $description = 'Poll the hub for pending deployment requests and execute them';

    protected string $basePath = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';
    protected string $branch   = 'db-sync-v2';
    protected string $hubUrl = 'https://sync.hamirglobal.com/api/deploy/pending';

    protected ?string $deployToken = null;

    public function __construct()
    {
        parent::__construct();
        $this->deployToken = $this->getDeployToken();
    }

    protected function getDeployToken(): ?string
    {
        $token = env('DEPLOY_TOKEN');
        
        if (!empty($token)) {
            return $token;
        }

        $envPath = base_path('.env');
        
        if (file_exists($envPath) && is_readable($envPath)) {
            try {
                $envContent = file_get_contents($envPath);
                $lines = explode("\n", $envContent);
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    
                    if (empty($line) || strpos($line, '#') === 0) {
                        continue;
                    }
                    
                    if (strpos($line, 'DEPLOY_TOKEN=') === 0) {
                        $parts = explode('=', $line, 2);
                        if (count($parts) === 2) {
                            $value = trim($parts[1]);
                            $value = trim($value, '"\'');
                            
                            if (!empty($value)) {
                                Log::info('DEPLOY_TOKEN loaded directly from .env file');
                                return $value;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Failed to read .env file directly', ['error' => $e->getMessage()]);
            }
        }

        return null;
    }

    public function handle()
    {
        if (empty($this->deployToken)) {
            $this->error('DEPLOY_TOKEN is not set in your .env file.');
            $this->error('Please add: DEPLOY_TOKEN=your_token_here');
            $this->info('Try running: php artisan config:clear');
            return 1;
        }

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
            Log::error('PollDeployment error', ['error' => $e->getMessage()]);
        }

        return 0;
    }

    protected function runDeployment()
    {
        $version = now()->format('Ymd_His');
        $releasePath = "{$this->basePath}\\releases\\{$version}";
        $sharedPath  = "{$this->basePath}\\shared";
        $currentPath = "{$this->basePath}\\current";

        if (!is_dir("{$this->basePath}\\releases")) {
            mkdir("{$this->basePath}\\releases", 0755, true);
        }

        if (!is_dir($releasePath)) {
            mkdir($releasePath, 0755, true);
        }

        $repoUrl = $this->getGitRepoUrl();

        $this->info("Cloning from: {$repoUrl}");
        $this->info("Branch: {$this->branch}");

        $commands = [
            [
                'command' => "git clone -b {$this->branch} {$repoUrl} {$releasePath}",
                'cwd' => $this->basePath,
                'description' => 'Cloning repository'
            ],
            [
                'command' => "powershell -Command \"if (Test-Path '{$releasePath}\\storage') { Remove-Item -Path '{$releasePath}\\storage' -Recurse -Force }\"",
                'cwd' => $releasePath,
                'description' => 'Removing cloned storage directory'
            ],
            [
                'command' => "mklink {$releasePath}\\.env {$sharedPath}\\.env",
                'cwd' => $releasePath,
                'description' => 'Creating .env symlink'
            ],
            [
                'command' => "mklink /J {$releasePath}\\storage {$sharedPath}\\storage",
                'cwd' => $releasePath,
                'description' => 'Creating storage symlink'
            ],
            [
                // Add --working-dir to ensure composer only scans this release
                'command' => "composer install --no-dev --optimize-autoloader --no-interaction --working-dir=\"{$releasePath}\"",
                'cwd' => $releasePath,
                'description' => 'Installing composer dependencies'
            ],
            [
                'command' => 'php artisan migrate --force',
                'cwd' => $releasePath,
                'description' => 'Running migrations'
            ],
            [
                'command' => 'php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan optimize',
                'cwd' => $releasePath,
                'description' => 'Clearing cache and optimizing application'
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

                // Clean up failed release
                $this->cleanupFailedRelease($releasePath);

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

        $this->switchRelease($currentPath, $releasePath);

        $this->info("✓ Deployment completed successfully: {$version}");
    }

    protected function switchRelease(string $currentPath, string $releasePath)
    {
        if (file_exists($currentPath)) {
            $this->info("Removing existing 'current' link...");
            
            $removeProcess = Process::fromShellCommandline(
                "powershell -Command \"if (Test-Path '{$currentPath}') { " .
                "(Get-Item '{$currentPath}').Delete() }\"",
                dirname($currentPath)
            );

            $removeProcess->run();

            if (!$removeProcess->isSuccessful()) {
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

    protected function cleanupFailedRelease(string $releasePath)
    {
        $this->warn("Cleaning up failed release: {$releasePath}");
        
        try {
            $process = Process::fromShellCommandline(
                "powershell -Command \"Remove-Item -Path '{$releasePath}' -Recurse -Force\"",
                dirname($releasePath)
            );

            $process->setTimeout(120);
            $process->run();

            if ($process->isSuccessful()) {
                $this->info("✓ Cleaned up failed release");
            }
        } catch (\Throwable $e) {
            $this->warn("Could not cleanup failed release: " . $e->getMessage());
        }
    }

    protected function getGitRepoUrl(): string
    {
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

        throw new \RuntimeException(
            'Could not determine git repository URL. ' .
            'Please set it manually in the command or ensure current directory has a .git folder.'
        );
    }

    protected function notifyHubCompleted(int $deploymentId, string $status, string $message)
    {
        if (empty($this->deployToken)) {
            Log::warning('Cannot notify hub: DEPLOY_TOKEN not configured');
            return;
        }

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