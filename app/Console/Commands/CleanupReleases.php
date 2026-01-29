<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CleanupReleases extends Command
{
    protected $signature = 'deploy:cleanup {--keep=5 : Number of releases to keep}';
    protected $description = 'Clean up old deployment releases';

    protected string $basePath = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';

    public function handle()
    {
        $releasesPath = "{$this->basePath}\\releases";
        $keep = (int) $this->option('keep');

        if (!is_dir($releasesPath)) {
            $this->error("Releases directory not found: {$releasesPath}");
            return 1;
        }

        $this->info("Scanning releases directory...");

        // Get all release directories
        $releases = [];
        $items = scandir($releasesPath);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = "{$releasesPath}\\{$item}";
            if (is_dir($path)) {
                $releases[] = [
                    'name' => $item,
                    'path' => $path,
                    'time' => filemtime($path)
                ];
            }
        }

        // Sort by time (newest first)
        usort($releases, function ($a, $b) {
            return $b['time'] - $a['time'];
        });

        $total = count($releases);
        $this->info("Found {$total} releases");

        if ($total <= $keep) {
            $this->info("Nothing to clean up. You have {$total} releases, keeping {$keep}.");
            return 0;
        }

        // Get current release path
        $currentPath = "{$this->basePath}\\current";
        $currentTarget = null;
        
        if (file_exists($currentPath) && is_link($currentPath)) {
            $currentTarget = readlink($currentPath);
            $currentTarget = basename($currentTarget);
            $this->info("Current release: {$currentTarget}");
        }

        // Keep the newest ones and current
        $toDelete = array_slice($releases, $keep);

        $this->warn("Will delete " . count($toDelete) . " old release(s):");
        
        foreach ($toDelete as $release) {
            // Don't delete the current release
            if ($currentTarget && $release['name'] === $currentTarget) {
                $this->line("  - {$release['name']} (SKIPPED - current release)");
                continue;
            }
            
            $this->line("  - {$release['name']}");
        }

        $this->newLine();

        if (!$this->confirm('Do you want to proceed?', true)) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $deleted = 0;
        foreach ($toDelete as $release) {
            // Skip current release
            if ($currentTarget && $release['name'] === $currentTarget) {
                continue;
            }

            $this->info("Deleting {$release['name']}...");

            try {
                $process = Process::fromShellCommandline(
                    "powershell -Command \"Remove-Item -Path '{$release['path']}' -Recurse -Force\"",
                    dirname($release['path'])
                );

                $process->setTimeout(120);
                $process->run();

                if ($process->isSuccessful()) {
                    $this->info("✓ Deleted {$release['name']}");
                    $deleted++;
                } else {
                    $this->error("✗ Failed to delete {$release['name']}");
                    $this->line("  Error: " . $process->getErrorOutput());
                }
            } catch (\Throwable $e) {
                $this->error("✗ Exception deleting {$release['name']}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("✓ Cleanup complete. Deleted {$deleted} release(s).");

        return 0;
    }
}