<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class RunDeployment extends Command
{
    protected $signature = 'deploy:run {branch=db-sync-v2}';
    protected $description = 'Run a deployment of the field server code';

    protected string $basePath = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';
    protected string $sharedPath;
    protected string $currentPath;

    public function __construct()
    {
        parent::__construct();
        $this->sharedPath  = "{$this->basePath}\\shared";
        $this->currentPath = "{$this->basePath}\\current";
    }

    public function handle()
    {
        $branch = $this->argument('branch');

        // Deployment lock file
        $lockFile = "{$this->basePath}\\deploy.lock";
        if (file_exists($lockFile)) {
            $this->error('Deployment already in progress. Exiting.');
            return 1;
        }

        file_put_contents($lockFile, now()->toIso8601String());

        try {
            $version = now()->format('Ymd_His');
            $releasePath = "{$this->basePath}\\releases\\{$version}";

            // Ensure releases directory exists
            if (!is_dir("{$this->basePath}\\releases")) {
                mkdir("{$this->basePath}\\releases", 0755, true);
            }

            // Create release directory
            if (!is_dir($releasePath)) {
                mkdir($releasePath, 0755, true);
            }

            // Get repository URL
            $repoUrl = $this->getGitRepoUrl();

            $this->info("Starting deployment: {$version}");
            $this->info("Repository: {$repoUrl}");
            $this->info("Branch: {$branch}");

            $commands = [
                [
                    'command' => "git clone -b {$branch} {$repoUrl} {$releasePath}",
                    'cwd' => $this->basePath,
                    'description' => 'Cloning repository'
                ],
                [
                    'command' => "powershell -Command \"if (Test-Path '{$releasePath}\\storage') { Remove-Item -Path '{$releasePath}\\storage' -Recurse -Force }\"",
                    'cwd' => $releasePath,
                    'description' => 'Removing cloned storage directory'
                ],
                [
                    'command' => "mklink {$releasePath}\\.env {$this->sharedPath}\\.env",
                    'cwd' => $releasePath,
                    'description' => 'Creating .env symlink'
                ],
                [
                    'command' => "mklink /J {$releasePath}\\storage {$this->sharedPath}\\storage",
                    'cwd' => $releasePath,
                    'description' => 'Creating storage symlink'
                ],
                [
                    'command' => 'composer install --no-dev --optimize-autoloader --no-interaction',
                    'cwd' => $releasePath,
                    'description' => 'Installing dependencies'
                ],
                [
                    'command' => 'php artisan migrate --force',
                    'cwd' => $releasePath,
                    'description' => 'Running migrations'
                ],
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

                $process->run(function ($type, $buffer) {
                    // Real-time output
                    if (strlen($buffer) > 0) {
                        $this->line(rtrim($buffer));
                    }
                });

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

                    $this->error("✗ {$item['description']} failed!");
                    $this->error("Exit code: {$process->getExitCode()}");
                    $this->error("Output: {$fullOutput}");

                    $this->rollback($releasePath);
                    unlink($lockFile);
                    return 1;
                }

                $this->info("✓ {$item['description']} completed");

                if ($output || $errorOutput) {
                    Log::info("Deployment step output", [
                        'step' => $item['description'],
                        'output' => $output ?: $errorOutput,
                    ]);
                }
            }

            // Switch to new release
            $this->switchRelease($releasePath);

            unlink($lockFile);
            $this->info("🎉 Deployment completed successfully: {$version}");
            return 0;
        } catch (\Throwable $e) {
            if (isset($releasePath)) {
                $this->rollback($releasePath);
            }

            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            Log::error('Deployment exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error("Deployment exception: " . $e->getMessage());
            return 1;
        }
    }

    protected function switchRelease(string $releasePath)
    {
        $this->info("→ Switching to new release...");

        // Remove old symlink/directory
        if (is_link($this->currentPath)) {
            rmdir($this->currentPath);
        } elseif (is_dir($this->currentPath)) {
            $backup = $this->currentPath . '_backup_' . now()->format('Ymd_His');
            rename($this->currentPath, $backup);
            $this->warn("Backed up old current directory to: {$backup}");
        }

        // Create new symlink
        $process = Process::fromShellCommandline(
            "mklink /J {$this->currentPath} {$releasePath}",
            dirname($this->currentPath)
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                "Failed to create symlink.\n" .
                    "Command: mklink /J {$this->currentPath} {$releasePath}\n" .
                    "Error: " . $process->getErrorOutput()
            );
        }

        $this->info("✓ Switched to new release");
    }

    protected function getGitRepoUrl(): string
    {
        // Try to get from current directory
        if (is_dir("{$this->currentPath}\\.git")) {
            $process = Process::fromShellCommandline(
                'git config --get remote.origin.url',
                $this->currentPath
            );

            $process->run();

            if ($process->isSuccessful()) {
                return trim($process->getOutput());
            }
        }

        throw new \RuntimeException(
            'Could not determine git repository URL. ' .
                'Make sure the current directory has a .git folder or specify the repo URL manually.'
        );
    }

    protected function rollback($releasePath)
    {
        $this->warn("⚠ Rolling back deployment...");
        Log::warning('Rolling back deployment', ['release' => $releasePath]);

        if (is_dir($releasePath)) {
            try {
                // Use PowerShell for better Windows compatibility
                $process = Process::fromShellCommandline(
                    "powershell -Command \"Remove-Item -Path '{$releasePath}' -Recurse -Force\"",
                    dirname($releasePath)
                );

                $process->setTimeout(120);
                $process->run();

                if ($process->isSuccessful()) {
                    $this->info("✓ Rolled back successfully");
                } else {
                    $this->warn("Could not remove failed release directory. Manual cleanup may be required.");
                    $this->warn("Path: {$releasePath}");
                }
            } catch (\Throwable $e) {
                $this->warn("Rollback cleanup error: " . $e->getMessage());
            }
        }
    }
}
