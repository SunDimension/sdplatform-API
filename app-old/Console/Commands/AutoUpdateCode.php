<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Services\SyncNotificationService;

class AutoUpdateCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:auto-update 
                            {--check-only : Only check for updates without applying}
                            {--force : Force update even during business hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check and apply code updates from remote repository';

    protected $notificationService;

    public function __construct(SyncNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Don't run on central hub
        if (config('sync.is_central_hub', false)) {
            Log::info('Auto-update skipped: This is the central hub');
            return 0;
        }

        // Check if auto-updates are enabled
        if (!config('sync.auto_updates.enabled', false)) {
            Log::info('Auto-update skipped: Auto-updates disabled in config');
            return 0;
        }

        Log::info('Starting auto-update check', [
            'location_id' => config('app.location_id'),
            'check_only' => $this->option('check-only')
        ]);

        try {
            // Check for updates from remote
            $updateInfo = $this->checkForUpdates();

            if (!$updateInfo['has_updates']) {
                Log::info('No updates available', [
                    'current_commit' => $updateInfo['current_commit']
                ]);
                return 0;
            }

            Log::info('Updates available', [
                'current_commit' => $updateInfo['current_commit'],
                'latest_commit' => $updateInfo['latest_commit'],
                'commits_behind' => $updateInfo['commits_behind']
            ]);

            // If check-only, just notify and exit
            if ($this->option('check-only')) {
                $this->notifyUpdateAvailable($updateInfo);
                return 0;
            }

            // Check if we should update now (based on business hours)
            if (!$this->shouldUpdateNow() && !$this->option('force')) {
                Log::info('Update postponed: Outside update window');
                $this->scheduleUpdateForLater();
                return 0;
            }

            // Apply the update
            $result = $this->applyUpdate($updateInfo);

            if ($result['success']) {
                Log::info('Auto-update completed successfully', [
                    'new_commit' => $result['new_commit']
                ]);
                $this->notifyUpdateSuccess($updateInfo, $result);
                return 0;
            } else {
                Log::error('Auto-update failed', [
                    'errors' => $result['errors']
                ]);
                $this->notifyUpdateFailure($updateInfo, $result);
                return 1;
            }

        } catch (\Exception $e) {
            Log::error('Auto-update process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->notifyUpdateFailure([], ['errors' => [$e->getMessage()]]);
            return 1;
        }
    }

    /**
     * Check for available updates from remote repository
     */
    protected function checkForUpdates(): array
    {
        $result = [
            'has_updates' => false,
            'current_commit' => null,
            'latest_commit' => null,
            'commits_behind' => 0,
            'changelog' => []
        ];

        try {
            // Fetch latest changes from remote
            $this->runProcess(['git', 'fetch', 'origin']);

            // Get current commit
            $currentCommit = trim($this->runProcess(['git', 'rev-parse', 'HEAD']));
            $result['current_commit'] = $currentCommit;

            // Get current branch
            $branch = config('sync.auto_updates.branch', 'main');
            
            // Get latest commit on remote branch
            $latestCommit = trim($this->runProcess([
                'git', 'rev-parse', "origin/{$branch}"
            ]));
            $result['latest_commit'] = $latestCommit;

            // Check if we're behind
            if ($currentCommit !== $latestCommit) {
                $result['has_updates'] = true;

                // Count commits behind
                $commitsBehind = trim($this->runProcess([
                    'git', 'rev-list', '--count', "{$currentCommit}..{$latestCommit}"
                ]));
                $result['commits_behind'] = (int) $commitsBehind;

                // Get changelog
                $changelog = $this->runProcess([
                    'git', 'log', '--oneline', "{$currentCommit}..{$latestCommit}"
                ]);
                $result['changelog'] = array_filter(explode("\n", trim($changelog)));
            }

        } catch (\Exception $e) {
            Log::error('Failed to check for updates', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return $result;
    }

    /**
     * Apply the update
     */
    protected function applyUpdate(array $updateInfo): array
    {
        $result = [
            'success' => false,
            'new_commit' => null,
            'errors' => []
        ];

        try {
            $branch = config('sync.auto_updates.branch', 'main');

            Log::info('Starting update application', [
                'from_commit' => $updateInfo['current_commit'],
                'to_commit' => $updateInfo['latest_commit'],
                'branch' => $branch
            ]);

            // Create backup marker
            $this->createBackupMarker($updateInfo);

            // Stash any local changes
            $this->runProcess(['git', 'stash', 'save', 'Auto-update stash - ' . date('Y-m-d H:i:s')]);

            // Put application in maintenance mode
            Artisan::call('down', ['--retry' => 60, '--secret' => config('sync.auto_updates.bypass_key', 'update-in-progress')]);
            Log::info('Application in maintenance mode');

            try {
                // Pull latest changes
                $pullOutput = $this->runProcess(['git', 'pull', 'origin', $branch]);
                Log::info('Git pull completed', ['output' => $pullOutput]);

                // Update Composer dependencies if composer.lock changed
                if ($this->composerLockChanged($updateInfo)) {
                    Log::info('Composer.lock changed, updating dependencies');
                    $this->updateComposerDependencies();
                }

                // Run database migrations
                Log::info('Running migrations');
                Artisan::call('migrate', ['--force' => true]);
                $migrationOutput = Artisan::output();
                Log::info('Migrations completed', ['output' => $migrationOutput]);

                // Clear all caches
                $this->clearAllCaches();

                // Optimize application
                Artisan::call('optimize');
                Log::info('Application optimized');

                // Get new commit hash
                $result['new_commit'] = trim($this->runProcess(['git', 'rev-parse', 'HEAD']));

                // Bring application back online
                Artisan::call('up');
                Log::info('Application back online');

                $result['success'] = true;

            } catch (\Exception $e) {
                // Something went wrong, try to revert
                Log::error('Update failed, attempting to revert', [
                    'error' => $e->getMessage()
                ]);

                $this->revertUpdate($updateInfo);
                
                // Bring application back online even after failure
                Artisan::call('up');
                
                throw $e;
            }

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
            
            Log::error('Update application failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $result;
    }

    /**
     * Check if we should update now based on configuration
     */
    protected function shouldUpdateNow(): bool
    {
        $config = config('sync.auto_updates', []);

        // If immediate updates enabled, always update
        if ($config['immediate'] ?? false) {
            return true;
        }

        // Check if we're in the allowed update window
        $updateWindow = $config['update_window'] ?? [];
        
        if (empty($updateWindow)) {
            // No window configured, allow updates anytime
            return true;
        }

        $now = now();
        $currentHour = $now->hour;
        $currentDay = $now->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        // Check day of week
        $allowedDays = $updateWindow['days'] ?? [0,1,2,3,4,5,6]; // All days by default
        if (!in_array($currentDay, $allowedDays)) {
            return false;
        }

        // Check hour range
        $startHour = $updateWindow['start_hour'] ?? 2; // 2 AM default
        $endHour = $updateWindow['end_hour'] ?? 6; // 6 AM default

        if ($currentHour >= $startHour && $currentHour < $endHour) {
            return true;
        }

        // Check if it's a maintenance window
        $maintenanceMode = Cache::get('force_maintenance_window', false);
        if ($maintenanceMode) {
            return true;
        }

        return false;
    }

    /**
     * Schedule update for later
     */
    protected function scheduleUpdateForLater(): void
    {
        $updateWindow = config('sync.auto_updates.update_window', []);
        $startHour = $updateWindow['start_hour'] ?? 2;

        $nextUpdate = now()->setTime($startHour, 0, 0);
        
        // If we've passed today's window, schedule for tomorrow
        if ($nextUpdate->isPast()) {
            $nextUpdate->addDay();
        }

        Cache::put('pending_code_update', true, $nextUpdate);
        
        Log::info('Update scheduled for later', [
            'scheduled_time' => $nextUpdate->toDateTimeString()
        ]);
    }

    /**
     * Check if composer.lock has changed
     */
    protected function composerLockChanged(array $updateInfo): bool
    {
        try {
            $changes = $this->runProcess([
                'git', 'diff', '--name-only',
                $updateInfo['current_commit'],
                $updateInfo['latest_commit']
            ]);

            return str_contains($changes, 'composer.lock');
        } catch (\Exception $e) {
            // If we can't determine, assume it changed to be safe
            return true;
        }
    }

    /**
     * Update Composer dependencies
     */
    protected function updateComposerDependencies(): void
    {
        try {
            $output = $this->runProcess([
                'composer',
                'install',
                '--no-dev',
                '--optimize-autoloader',
                '--no-interaction'
            ], timeout: 300);
            
            Log::info('Composer dependencies updated', ['output' => $output]);
        } catch (\Exception $e) {
            Log::error('Composer update failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Clear all application caches
     */
    protected function clearAllCaches(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('event:clear');
        
        Log::info('All caches cleared');
    }

    /**
     * Create backup marker
     */
    protected function createBackupMarker(array $updateInfo): void
    {
        $backupData = [
            'commit' => $updateInfo['current_commit'],
            'timestamp' => now()->toISOString(),
            'location_id' => config('app.location_id')
        ];

        Cache::put('last_code_backup', $backupData, now()->addDays(7));
        
        // Also save to file for persistence
        $backupFile = storage_path('app/backups/last_commit.json');
        $backupDir = dirname($backupFile);
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));
    }

    /**
     * Revert update
     */
    protected function revertUpdate(array $updateInfo): void
    {
        try {
            Log::info('Reverting to previous commit', [
                'target_commit' => $updateInfo['current_commit']
            ]);

            // Reset to previous commit
            $this->runProcess(['git', 'reset', '--hard', $updateInfo['current_commit']]);
            
            // Try to restore stashed changes
            try {
                $this->runProcess(['git', 'stash', 'pop']);
            } catch (\Exception $e) {
                // Stash pop might fail if there are conflicts, that's okay
                Log::warning('Could not restore stashed changes', [
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Reverted to previous commit successfully');

        } catch (\Exception $e) {
            Log::error('Failed to revert update', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify that update is available
     */
    protected function notifyUpdateAvailable(array $updateInfo): void
    {
        $message = sprintf(
            "Code update available for location %s\nCommits behind: %d\nCurrent: %s\nLatest: %s",
            config('app.location_id'),
            $updateInfo['commits_behind'],
            substr($updateInfo['current_commit'], 0, 7),
            substr($updateInfo['latest_commit'], 0, 7)
        );

        Log::info($message);

        // Send notification if enabled
        if (config('sync.notifications.enabled') && config('sync.auto_updates.notify_available', true)) {
            try {
                $this->notificationService->notifySyncFailure([
                    'type' => 'code_update_available',
                    'message' => $message,
                    'update_info' => $updateInfo
                ], 'code_update');
            } catch (\Exception $e) {
                Log::warning('Failed to send update notification', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Notify update success
     */
    protected function notifyUpdateSuccess(array $updateInfo, array $result): void
    {
        $message = sprintf(
            "Code update completed successfully for location %s\nFrom: %s\nTo: %s\nCommits applied: %d",
            config('app.location_id'),
            substr($updateInfo['current_commit'], 0, 7),
            substr($result['new_commit'], 0, 7),
            $updateInfo['commits_behind']
        );

        Log::info($message);

        if (config('sync.notifications.enabled') && config('sync.auto_updates.notify_success', true)) {
            try {
                $this->notificationService->notifySyncFailure([
                    'type' => 'code_update_success',
                    'message' => $message,
                    'update_info' => $updateInfo,
                    'result' => $result
                ], 'code_update_success');
            } catch (\Exception $e) {
                Log::warning('Failed to send success notification', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Notify update failure
     */
    protected function notifyUpdateFailure(array $updateInfo, array $result): void
    {
        $message = sprintf(
            "Code update FAILED for location %s\nErrors: %s",
            config('app.location_id'),
            implode(', ', $result['errors'] ?? ['Unknown error'])
        );

        Log::error($message);

        if (config('sync.notifications.enabled') && config('sync.auto_updates.notify_failure', true)) {
            try {
                $this->notificationService->notifySyncFailure([
                    'type' => 'code_update_failure',
                    'message' => $message,
                    'update_info' => $updateInfo,
                    'result' => $result
                ], 'code_update_failure');
            } catch (\Exception $e) {
                Log::warning('Failed to send failure notification', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Run a shell command
     */
    protected function runProcess(array $command, int $timeout = 60): string
    {
        $process = new Process($command, base_path());
        $process->setTimeout($timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}