<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncSchedulerService;
use App\Services\SyncService;
use Illuminate\Support\Facades\Log;

class ScheduledSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute scheduled data synchronization';

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
        $this->info('🕐 Checking scheduled synchronization...');
        
        try {
            // Check if we should run now
            if (!$this->schedulerService->shouldRunNow()) {
                $nextRun = $this->schedulerService->getNextRunTime();
                $this->info("⏰ Next sync scheduled for: {$nextRun->format('Y-m-d H:i:s')}");
                return 0;
            }

            $this->info('✅ Conditions met for synchronization');
            
            // Get optimal sync mode
            $mode = $this->schedulerService->getOptimalSyncMode();
            $this->info("🔄 Optimal sync mode: {$mode}");
            
            // Execute synchronization
            $results = $this->executeSync($mode);
            
            // Log activity
            $this->schedulerService->logSyncActivity($mode, $results);
            
            // Update last run time
            $this->schedulerService->setLastRunTime(now());
            
            $this->info('✅ Scheduled synchronization completed successfully');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Scheduled synchronization failed: ' . $e->getMessage());
            Log::error('ScheduledSync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Execute synchronization based on mode
     */
    protected function executeSync(string $mode): array
    {
        $this->info("🚀 Executing {$mode} synchronization...");
        
        switch ($mode) {
            case 'push-only':
                return $this->syncService->pushChanges();
                
            case 'pull-only':
                return $this->syncService->pullChanges();
                
            case 'queue-only':
                return $this->syncService->processQueue();
                
            case 'full':
            default:
                // Execute full sync in sequence
                $pushResults = $this->syncService->pushChanges();
                $pullResults = $this->syncService->pullChanges();
                $queueResults = $this->syncService->processQueue();
                
                return [
                    'push' => $pushResults,
                    'pull' => $pullResults,
                    'queue' => $queueResults,
                    'total_success' => 
                        ($pushResults['success'] ?? 0) + 
                        ($pullResults['success'] ?? 0) + 
                        ($queueResults['processed'] ?? 0),
                    'total_failed' => 
                        ($pushResults['failed'] ?? 0) + 
                        ($pullResults['failed'] ?? 0) + 
                        ($queueResults['failed'] ?? 0)
                ];
        }
    }
}
