<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AutoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:auto 
                            {--mode=full : Sync mode (full, push-only, pull-only, queue-only)}
                            {--retry-failed : Retry failed sync items}
                            {--health-check : Perform health check before sync}
                            {--force : Force sync even if offline}
                            {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated data synchronization with intelligent retry and monitoring';

    protected SyncService $syncService;
    protected array $syncStats = [];
    protected float $startTime;
    protected int $progressBarSteps = 0;

    public function __construct(SyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Starting automated synchronization...');
        
        $this->startTime = microtime(true);
        $mode = $this->option('mode');
        $retryFailed = $this->option('retry-failed');
        $healthCheck = $this->option('health-check');
        $force = $this->option('force');
        $verbose = $this->option('detailed');

        // Initialize stats
        $this->syncStats = [
            'start_time' => now(),
            'mode' => $mode,
            'operations' => [],
            'total_success' => 0,
            'total_failed' => 0,
            'total_retries' => 0,
            'duration' => 0
        ];

        try {
            // Health check if requested
            if ($healthCheck) {
                $this->performHealthCheck();
            }

            // Check online status
            if (!$force && !$this->syncService->isOnline()) {
                $this->warn('⚠️  System appears offline. Processing queue only...');
                $mode = 'queue-only';
            }

            // Execute synchronization based on mode
            switch ($mode) {
                case 'push-only':
                    $this->executePushSync($verbose);
                    break;
                case 'pull-only':
                    $this->executePullSync($verbose);
                    break;
                case 'queue-only':
                    $this->executeQueueSync($verbose);
                    break;
                case 'full':
                default:
                    $this->executeFullSync($verbose);
                    break;
            }

            // Retry failed items if requested
            if ($retryFailed) {
                $this->retryFailedItems($verbose);
            }

            // Generate and display report
            $this->generateSyncReport($this->startTime, $verbose);

        } catch (\Exception $e) {
            $this->error('❌ Automated synchronization failed: ' . $e->getMessage());
            Log::error('AutoSync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'stats' => $this->syncStats
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Execute full synchronization with progress tracking
     */
    protected function executeFullSync(bool $verbose): void
    {
        $this->info('🔄 Executing full synchronization...');
        
        if ($verbose) {
            $this->progressBarSteps = 3;
            $this->info("📊 Progress: 0/{$this->progressBarSteps} operations completed");
        }
        
        // Push changes
        $this->executePushSync($verbose);
        if ($verbose) {
            $this->info("📊 Progress: 1/{$this->progressBarSteps} operations completed");
        }
        
        // Pull changes
        $this->executePullSync($verbose);
        if ($verbose) {
            $this->info("📊 Progress: 2/{$this->progressBarSteps} operations completed");
        }
        
        // Process queue
        $this->executeQueueSync($verbose);
        if ($verbose) {
            $this->info("📊 Progress: {$this->progressBarSteps}/{$this->progressBarSteps} operations completed");
        }
    }

    /**
     * Execute push synchronization
     */
    protected function executePushSync(bool $verbose): void
    {
        $this->info('📤 Pushing local changes...');
        
        try {
            $results = $this->syncService->pushChanges();
            
            $this->syncStats['operations']['push'] = $results;
            $this->syncStats['total_success'] += $results['success'];
            $this->syncStats['total_failed'] += $results['failed'];

            if ($verbose) {
                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Success', $results['success']],
                        ['Failed', $results['failed']]
                    ]
                );

                if (!empty($results['errors'])) {
                    $this->warn('⚠️  Push errors encountered:');
                    foreach ($results['errors'] as $error) {
                        $this->line("   - {$error['error']}");
                    }
                }
            } else {
                $this->info("✅ Push completed: {$results['success']} success, {$results['failed']} failed");
            }

        } catch (\Exception $e) {
            $this->error('❌ Push synchronization failed: ' . $e->getMessage());
            $this->syncStats['operations']['push'] = ['error' => $e->getMessage()];
            throw $e;
        }
    }

    /**
     * Execute pull synchronization
     */
    protected function executePullSync(bool $verbose): void
    {
        $this->info('📥 Pulling changes from central hub...');
        
        try {
            $results = $this->syncService->pullChanges();
            
            $this->syncStats['operations']['pull'] = $results;
            $this->syncStats['total_success'] += $results['success'];
            $this->syncStats['total_failed'] += $results['failed'];

            if ($verbose) {
                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Success', $results['success']],
                        ['Failed', $results['failed']]
                    ]
                );

                if (!empty($results['errors'])) {
                    $this->warn('⚠️  Pull errors encountered:');
                    foreach ($results['errors'] as $error) {
                        $this->line("   - {$error['error']}");
                    }
                }
            } else {
                $this->info("✅ Pull completed: {$results['success']} success, {$results['failed']} failed");
            }

        } catch (\Exception $e) {
            $this->error('❌ Pull synchronization failed: ' . $e->getMessage());
            $this->syncStats['operations']['pull'] = ['error' => $e->getMessage()];
            
            // Don't throw for pull failures - they're often external
            $this->warn('⚠️  Continuing with other operations...');
        }
    }

    /**
     * Execute queue processing with batch optimization
     */
    protected function executeQueueSync(bool $verbose): void
    {
        $this->info('🔄 Processing sync queue...');
        
        try {
            $batchSize = config('sync.batch_size', 100);
            $maxMemory = config('sync.max_memory_mb', 512);
            $startMemory = memory_get_usage(true);
            
            $this->info("📊 Batch size: {$batchSize}, Max memory: {$maxMemory}MB");
            
            $results = $this->syncService->processQueue($batchSize);
            
            $this->syncStats['operations']['queue'] = $results;
            $this->syncStats['total_success'] += $results['processed'];
            $this->syncStats['total_failed'] += $results['failed'];
            
            // Memory usage tracking
            $endMemory = memory_get_usage(true);
            $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);
            $this->syncStats['memory_usage_mb'] = $memoryUsed;

            if ($verbose) {
                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Processed', $results['processed']],
                        ['Failed', $results['failed']],
                        ['Memory Used', "{$memoryUsed}MB"]
                    ]
                );

                if (!empty($results['errors'])) {
                    $this->warn('⚠️  Queue processing errors:');
                    foreach ($results['errors'] as $error) {
                        $this->line("   - Item {$error['item_id']}: {$error['error']}");
                    }
                }
            } else {
                $this->info("✅ Queue processing completed: {$results['processed']} processed, {$results['failed']} failed ({$memoryUsed}MB)");
            }

        } catch (\Exception $e) {
            $this->error('❌ Queue processing failed: ' . $e->getMessage());
            $this->syncStats['operations']['queue'] = ['error' => $e->getMessage()];
            throw $e;
        }
    }

    /**
     * Retry failed synchronization items
     */
    protected function retryFailedItems(bool $verbose): void
    {
        $this->info('🔄 Retrying failed items...');
        
        try {
            // Get failed items count
            $failedCount = $this->getFailedItemsCount();
            
            if ($failedCount === 0) {
                $this->info('✅ No failed items to retry');
                return;
            }

            $this->info("🔄 Found {$failedCount} failed items. Retrying...");
            
            // Process queue with focus on failed items
            $results = $this->syncService->processQueue();
            
            $this->syncStats['total_retries'] = $results['processed'];
            
            if ($verbose) {
                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Retried', $results['processed']],
                        ['Still Failed', $results['failed']]
                    ]
                );
            } else {
                $this->info("✅ Retry completed: {$results['processed']} retried, {$results['failed']} still failed");
            }

        } catch (\Exception $e) {
            $this->error('❌ Retry operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Perform health check
     */
    protected function performHealthCheck(): void
    {
        $this->info('🏥 Performing health check...');
        
        try {
            $isOnline = $this->syncService->isOnline();
            $pendingCount = $this->getPendingItemsCount();
            $failedCount = $this->getFailedItemsCount();
            
            $this->syncStats['health_check'] = [
                'online' => $isOnline,
                'pending' => $pendingCount,
                'failed' => $failedCount,
                'timestamp' => now()
            ];

            $this->table(
                ['Metric', 'Status'],
                [
                    ['Online Status', $isOnline ? '✅ Online' : '❌ Offline'],
                    ['Pending Items', $pendingCount],
                    ['Failed Items', $failedCount]
                ]
            );

        } catch (\Exception $e) {
            $this->warn('⚠️  Health check failed: ' . $e->getMessage());
            $this->syncStats['health_check'] = ['error' => $e->getMessage()];
        }
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
     * Generate synchronization report
     */
    protected function generateSyncReport(float $startTime, bool $verbose): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->syncStats['duration'] = $duration;

        $this->info('📊 Synchronization Report');
        $this->info('========================');

        if ($verbose) {
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Mode', $this->syncStats['mode']],
                    ['Total Success', $this->syncStats['total_success']],
                    ['Total Failed', $this->syncStats['total_failed']],
                    ['Total Retries', $this->syncStats['total_retries']],
                    ['Duration', "{$duration}ms"],
                    ['Status', '✅ Completed']
                ]
            );
        } else {
            $this->info("✅ Synchronization completed in {$duration}ms");
            $this->info("📈 Results: {$this->syncStats['total_success']} success, {$this->syncStats['total_failed']} failed");
        }

        // Cache stats for monitoring
        Cache::put('sync_stats_' . now()->format('Y-m-d_H-i-s'), $this->syncStats, now()->addDays(7));
        
        // Log completion
        Log::info('AutoSync completed', $this->syncStats);
    }
} 