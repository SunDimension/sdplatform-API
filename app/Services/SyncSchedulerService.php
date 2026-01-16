<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SyncQueue;

class SyncSchedulerService
{
    protected SyncService $syncService;
    protected array $scheduleConfig;
    protected string $locationId;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
        $this->scheduleConfig = config('sync.scheduler', []);
        $this->locationId = config('sync.location_id');
    }

    /**
     * Determine if synchronization should run now
     */
    public function shouldRunNow(): bool
    {
        // Check if we're in a scheduled window
        if (!$this->isInScheduledWindow()) {
            return false;
        }

        // Check if we have pending items
        if ($this->hasPendingItems()) {
            return true;
        }

        // Check if we're due for a regular sync
        if ($this->isDueForRegularSync()) {
            return true;
        }

        // Check if we have failed items that need retry
        if ($this->hasFailedItemsNeedingRetry()) {
            return true;
        }

        return false;
    }

    /**
     * Get next scheduled run time
     */
    public function getNextRunTime(): Carbon
    {
        $lastRun = $this->getLastRunTime();
        $interval = $this->getSyncInterval();
        
        return $lastRun->addMinutes($interval);
    }

    /**
     * Get optimal sync mode based on current conditions
     */
    // public function getOptimalSyncMode(): string
    // {
    //     // If offline, only process queue
    //     if (!$this->syncService->isOnline()) {
    //         return 'queue-only';
    //     }

    //     // If many pending items, do full sync
    //     if ($this->getPendingItemsCount() > 50) {
    //         return 'full';
    //     }

    //     // If few pending items, do push-only
    //     if ($this->getPendingItemsCount() > 0) {
    //         return 'push-only';
    //     }

    //     // Default to pull-only for regular updates
    //     return 'pull-only';
    // }

    public function getOptimalSyncMode(): string
{
    $pending = $this->getPendingItemsCount();

    if ($pending > 0) {
        return 'push-only';
    }

    if (!$this->syncService->isOnline()) {
        return 'queue-only';
    }

    return 'pull-only';
}


    /**
     * Get sync interval based on activity level
     */
    public function getSyncInterval(): int
    {
        $pendingCount = $this->getPendingItemsCount();
        $failedCount = $this->getFailedItemsCount();
        
        // High activity - sync more frequently
        if ($pendingCount > 100 || $failedCount > 10) {
            return $this->scheduleConfig['high_activity_interval'] ?? 5;
        }
        
        // Medium activity
        if ($pendingCount > 20 || $failedCount > 5) {
            return $this->scheduleConfig['medium_activity_interval'] ?? 15;
        }
        
        // Low activity - sync less frequently
        return $this->scheduleConfig['low_activity_interval'] ?? 30;
    }

    /**
     * Check if we're in a scheduled window
     */
    protected function isInScheduledWindow(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek;
        $hour = $now->hour;
        
        // Check business hours (Monday-Friday, 8 AM - 6 PM)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5 && $hour >= 8 && $hour < 18) {
            return true;
        }
        
        // Check if we have critical items that need immediate attention
        if ($this->hasCriticalItems()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if we have pending items
     */
    protected function hasPendingItems(): bool
    {
        return $this->getPendingItemsCount() > 0;
    }

    /**
     * Check if we're due for regular sync
     */
    protected function isDueForRegularSync(): bool
    {
        $lastRun = $this->getLastRunTime();
        $interval = $this->getSyncInterval();
        
        return $lastRun->addMinutes($interval)->isPast();
    }

    /**
     * Check if we have failed items needing retry
     */
    protected function hasFailedItemsNeedingRetry(): bool
    {
        $failedCount = $this->getFailedItemsCount();
        $lastRetry = $this->getLastRetryTime();
        $retryInterval = $this->scheduleConfig['retry_interval'] ?? 60;
        
        return $failedCount > 0 && $lastRetry->addMinutes($retryInterval)->isPast();
    }

    /**
     * Check if we have critical items
     */
    protected function hasCriticalItems(): bool
    {
        // Check for high-priority sync queue items
        $criticalCount = SyncQueue::where('priority', '>=', 8)
            ->where('status', 'pending')
            ->count();
            
        return $criticalCount > 0;
    }

    /**
     * Get pending items count
     */
    protected function getPendingItemsCount(): int
    {
        try {
            $syncableModels = config('sync.models', []);
            $count = 0;
            
            foreach (array_keys($syncableModels) as $modelClass) {
                if (class_exists($modelClass) && method_exists($modelClass, 'scopeBySyncStatus')) {
                    $count += $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->count();
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get failed items count
     */
    protected function getFailedItemsCount(): int
    {
        try {
            $syncableModels = config('sync.models', []);
            $count = 0;
            
            foreach (array_keys($syncableModels) as $modelClass) {
                if (class_exists($modelClass) && method_exists($modelClass, 'scopeBySyncStatus')) {
                    $count += $modelClass::bySyncStatus('failed')->count();
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last run time
     */
    protected function getLastRunTime(): Carbon
    {
        $lastRun = Cache::get("sync_last_run_{$this->locationId}");
        
        if (!$lastRun) {
            // Default to 1 hour ago if no record
            $lastRun = now()->subHour();
            $this->setLastRunTime($lastRun);
        }
        
        return Carbon::parse($lastRun);
    }

    /**
     * Set last run time
     */
    public function setLastRunTime(Carbon $time): void
    {
        Cache::put("sync_last_run_{$this->locationId}", $time->toISOString(), now()->addDays(7));
    }

    /**
     * Get last retry time
     */
    protected function getLastRetryTime(): Carbon
    {
        $lastRetry = Cache::get("sync_last_retry_{$this->locationId}");
        
        if (!$lastRetry) {
            // Default to 1 hour ago if no record
            $lastRetry = now()->subHour();
            $this->setLastRetryTime($lastRetry);
        }
        
        return Carbon::parse($lastRetry);
    }

    /**
     * Set last retry time
     */
    public function setLastRetryTime(Carbon $time): void
    {
        Cache::put("sync_last_retry_{$this->locationId}", $time->toISOString(), now()->addDays(7));
    }

    /**
     * Get sync statistics for monitoring
     */
    public function getSyncStats(): array
    {
        return [
            'location_id' => $this->locationId,
            'last_run' => $this->getLastRunTime()->toISOString(),
            'next_run' => $this->getNextRunTime()->toISOString(),
            'pending_items' => $this->getPendingItemsCount(),
            'failed_items' => $this->getFailedItemsCount(),
            'sync_interval' => $this->getSyncInterval(),
            'optimal_mode' => $this->getOptimalSyncMode(),
            'in_scheduled_window' => $this->isInScheduledWindow(),
            'should_run_now' => $this->shouldRunNow(),
            'critical_items' => $this->hasCriticalItems()
        ];
    }

    /**
     * Log sync activity
     */
    public function logSyncActivity(string $mode, array $results): void
    {
        $stats = [
            'location_id' => $this->locationId,
            'mode' => $mode,
            'timestamp' => now()->toISOString(),
            'results' => $results,
            'next_run' => $this->getNextRunTime()->toISOString()
        ];
        
        Log::info('Scheduled sync completed', $stats);
        
        // Store in cache for monitoring
        $key = "sync_activity_{$this->locationId}_" . now()->format('Y-m-d_H');
        Cache::put($key, $stats, now()->addDays(1));
    }
}
