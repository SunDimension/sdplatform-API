<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncSchedulerService;
use App\Services\SyncService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:monitor 
                            {--live : Show live updates every 30 seconds}
                            {--history : Show sync history}
                            {--stats : Show detailed statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor synchronization system status and performance';

    protected SyncSchedulerService $schedulerService;
    protected SyncService $syncService;

    public function __construct(SyncSchedulerService $schedulerService, SyncService $syncService)
    {
        parent::__construct();
        $this->schedulerService = $schedulerService;
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $live = $this->option('live');
        $history = $this->option('history');
        $stats = $this->option('stats');

        if ($live) {
            $this->startLiveMonitoring();
        } elseif ($history) {
            $this->showSyncHistory();
        } elseif ($stats) {
            $this->showDetailedStats();
        } else {
            $this->showCurrentStatus();
        }

        return 0;
    }

    /**
     * Show current synchronization status
     */
    protected function showCurrentStatus(): void
    {
        $this->info('📊 Synchronization System Status');
        $this->info('================================');
        
        // Get scheduler stats
        $schedulerStats = $this->schedulerService->getSyncStats();
        
        // Check online status
        $isOnline = $this->syncService->isOnline();
        
        // Get queue status
        $queueStats = $this->getQueueStats();
        
        // Display status table
        $this->table(
            ['Metric', 'Status', 'Value'],
            [
                ['System Status', $isOnline ? '🟢 Online' : '🔴 Offline', ''],
                ['Location ID', '', $schedulerStats['location_id']],
                ['Last Sync', '', $schedulerStats['last_run']],
                ['Next Sync', '', $schedulerStats['next_run']],
                ['Pending Items', '', $schedulerStats['pending_items']],
                ['Failed Items', '', $schedulerStats['failed_items']],
                ['Sync Interval', '', $schedulerStats['sync_interval'] . ' min'],
                ['Optimal Mode', '', $schedulerStats['optimal_mode']],
                ['Business Hours', '', $schedulerStats['in_scheduled_window'] ? '✅ Active' : '❌ Inactive'],
                ['Should Run Now', '', $schedulerStats['should_run_now'] ? '✅ Yes' : '❌ No'],
                ['Critical Items', '', $schedulerStats['critical_items'] ? '⚠️  Yes' : '✅ No'],
            ]
        );
        
        // Display queue status
        $this->info('');
        $this->info('🔄 Queue Status');
        $this->info('===============');
        $this->table(
            ['Queue', 'Count', 'Status'],
            [
                ['Pending', $queueStats['pending'], '⏳'],
                ['Processing', $queueStats['processing'], '🔄'],
                ['Completed', $queueStats['completed'], '✅'],
                ['Failed', $queueStats['failed'], '❌'],
                ['Total', $queueStats['total'], '📊'],
            ]
        );
        
        // Display recommendations
        $this->showRecommendations($schedulerStats, $queueStats);
    }

    /**
     * Start live monitoring
     */
    protected function startLiveMonitoring(): void
    {
        $this->info('🔄 Starting live monitoring (Press Ctrl+C to stop)...');
        $this->info('');
        
        while (true) {
            // Clear screen (works on most terminals)
            if (function_exists('system')) {
                system('clear');
            }
            
            $this->showCurrentStatus();
            
            // Show timestamp
            $this->info('');
            $this->info('Last updated: ' . now()->format('Y-m-d H:i:s'));
            $this->info('Press Ctrl+C to stop monitoring');
            
            // Wait 30 seconds
            sleep(30);
        }
    }

    /**
     * Show synchronization history
     */
    protected function showSyncHistory(): void
    {
        $this->info('📚 Synchronization History');
        $this->info('==========================');
        
        // Get recent sync activities from cache
        $activities = $this->getRecentSyncActivities();
        
        if (empty($activities)) {
            $this->warn('No recent sync activities found');
            return;
        }
        
        $rows = [];
        foreach ($activities as $activity) {
            $rows[] = [
                $activity['timestamp'],
                $activity['mode'],
                $activity['location_id'],
                $activity['status'] ?? 'N/A',
                $activity['duration'] ?? 'N/A'
            ];
        }
        
        $this->table(
            ['Timestamp', 'Mode', 'Location', 'Status', 'Duration'],
            $rows
        );
    }

    /**
     * Show detailed statistics
     */
    protected function showDetailedStats(): void
    {
        $this->info('📈 Detailed Synchronization Statistics');
        $this->info('=====================================');
        
        // Performance metrics
        $performance = $this->getPerformanceMetrics();
        
        $this->info('🚀 Performance Metrics');
        $this->table(
            ['Metric', 'Value', 'Threshold'],
            [
                ['Avg Sync Duration', $performance['avg_duration'] . 'ms', '≤ 5000ms'],
                ['Max Sync Duration', $performance['max_duration'] . 'ms', '≤ 10000ms'],
                ['Success Rate', $performance['success_rate'] . '%', '≥ 95%'],
                ['Memory Usage', $performance['avg_memory'] . 'MB', '≤ 512MB'],
                ['Queue Processing Rate', $performance['queue_rate'] . '/min', '≥ 100/min'],
            ]
        );
        
        // Model-specific stats
        $this->info('');
        $this->info('📋 Model Synchronization Stats');
        $modelStats = $this->getModelSyncStats();
        
        $rows = [];
        foreach ($modelStats as $model => $stats) {
            $rows[] = [
                class_basename($model),
                $stats['pending'],
                $stats['synced'],
                $stats['failed'],
                $stats['last_sync']
            ];
        }
        
        $this->table(
            ['Model', 'Pending', 'Synced', 'Failed', 'Last Sync'],
            $rows
        );
    }

    /**
     * Get queue statistics
     */
    protected function getQueueStats(): array
    {
        try {
            return [
                'pending' => DB::table('sync_queues')->where('status', 'pending')->count(),
                'processing' => DB::table('sync_queues')->where('status', 'processing')->count(),
                'completed' => DB::table('sync_queues')->where('status', 'completed')->count(),
                'failed' => DB::table('sync_queues')->where('status', 'failed')->count(),
                'total' => DB::table('sync_queues')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'failed' => 0,
                'total' => 0,
            ];
        }
    }

    /**
     * Get recent sync activities
     */
    protected function getRecentSyncActivities(): array
    {
        $activities = [];
        $locationId = config('sync.location_id');
        
        // Get activities from the last 24 hours
        for ($i = 0; $i < 24; $i++) {
            $key = "sync_activity_{$locationId}_" . now()->subHours($i)->format('Y-m-d_H');
            $activity = Cache::get($key);
            if ($activity) {
                $activities[] = $activity;
            }
        }
        
        return array_slice($activities, 0, 10); // Return last 10 activities
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics(): array
    {
        // This would typically come from a metrics service or database
        // For now, return sample data
        return [
            'avg_duration' => 2500,
            'max_duration' => 8000,
            'success_rate' => 98.5,
            'avg_memory' => 256,
            'queue_rate' => 150,
        ];
    }

    /**
     * Get model-specific sync statistics
     */
    protected function getModelSyncStats(): array
    {
        $stats = [];
        $syncableModels = config('sync.models', []);
        
        foreach (array_keys($syncableModels) as $modelClass) {
            if (class_exists($modelClass) && method_exists($modelClass, 'scopeBySyncStatus')) {
                try {
                    $stats[$modelClass] = [
                        'pending' => $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->count(),
                        'synced' => $modelClass::where('sync_status', 'synced')->count(),
                        'failed' => $modelClass::bySyncStatus('failed')->count(),
                        'last_sync' => $this->getLastSyncTime($modelClass),
                    ];
                } catch (\Exception $e) {
                    $stats[$modelClass] = [
                        'pending' => 0,
                        'synced' => 0,
                        'failed' => 0,
                        'last_sync' => 'N/A',
                    ];
                }
            }
        }
        
        return $stats;
    }

    /**
     * Get last sync time for a model
     */
    protected function getLastSyncTime(string $modelClass): string
    {
        try {
            $lastSynced = $modelClass::whereNotNull('last_synced_at')
                ->orderBy('last_synced_at', 'desc')
                ->value('last_synced_at');
                
            return $lastSynced ? Carbon::parse($lastSynced)->diffForHumans() : 'Never';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Show recommendations based on current status
     */
    protected function showRecommendations(array $schedulerStats, array $queueStats): void
    {
        $this->info('');
        $this->info('💡 Recommendations');
        $this->info('==================');
        
        $recommendations = [];
        
        if ($queueStats['failed'] > 0) {
            $recommendations[] = '⚠️  Run "php artisan sync:auto --retry-failed" to retry failed items';
        }
        
        if ($queueStats['pending'] > 100) {
            $recommendations[] = '🔄 High pending items: Consider running "php artisan sync:auto --mode=full"';
        }
        
        if (!$schedulerStats['in_scheduled_window']) {
            $recommendations[] = '⏰ Outside business hours: Sync will run less frequently';
        }
        
        if ($schedulerStats['critical_items']) {
            $recommendations[] = '🚨 Critical items detected: Immediate attention required';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = '✅ System is running optimally';
        }
        
        foreach ($recommendations as $recommendation) {
            $this->line($recommendation);
        }
    }
}
