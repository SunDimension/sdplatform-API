<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DeployRollback extends Command
{
    protected $signature = 'deploy:rollback {version? : Specific release to roll back to}';
    protected $description = 'Rollback to previous or specified release';

    protected string $basePath;
    protected string $releasesPath;
    protected string $currentPath;
    protected string $lockFile;

    public function __construct()
    {
        parent::__construct();

        $this->basePath     = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';
        $this->releasesPath = $this->basePath . '\\releases';
        $this->currentPath  = $this->basePath . '\\current';
        $this->lockFile     = $this->basePath . '\\storage\\deploy.lock';
    }

    public function handle()
    {
        // 🔒 Deployment lock
        if (File::exists($this->lockFile)) {
            $this->error('Deployment locked. Another operation is running.');
            return Command::FAILURE;
        }

        File::put($this->lockFile, now());

        try {
            $targetVersion = $this->argument('version');

            if (! $targetVersion) {
                $targetVersion = $this->getPreviousRelease();
            }

            if (! $targetVersion) {
                $this->error('No previous release found.');
                return Command::FAILURE;
            }

            $targetPath = "{$this->releasesPath}\\{$targetVersion}";

            if (! is_dir($targetPath)) {
                $this->error("Release {$targetVersion} does not exist.");
                return Command::FAILURE;
            }

            $this->info("Rolling back to: {$targetVersion}");

            // Switch symlink
            $commands = [
                "if exist \"{$this->currentPath}\" rmdir \"{$this->currentPath}\"",
                "cmd /c mklink /J \"{$this->currentPath}\" \"{$targetPath}\"",
            ];

            foreach ($commands as $cmd) {
                $process = Process::fromShellCommandline($cmd);
                $process->run();

                if (! $process->isSuccessful()) {
                    throw new \RuntimeException($process->getErrorOutput());
                }
            }

            $this->info("Rollback completed successfully.");
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("Rollback failed: {$e->getMessage()}");
            return Command::FAILURE;
        } finally {
            File::delete($this->lockFile);
        }
    }

    /**
     * Get previous release (excluding current)
     */
    protected function getPreviousRelease(): ?string
    {
        if (! is_link($this->currentPath)) {
            return null;
        }

        $currentTarget = readlink($this->currentPath);
        $currentVersion = basename($currentTarget);

        $releases = collect(File::directories($this->releasesPath))
            ->map(fn ($dir) => basename($dir))
            ->sort()
            ->values();

        $currentIndex = $releases->search($currentVersion);

        return $currentIndex !== false && $currentIndex > 0
            ? $releases[$currentIndex - 1]
            : null;
    }
}
