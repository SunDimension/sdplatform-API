<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data 
                            {--push : Push local changes to central hub}
                            {--pull : Pull changes from central hub}
                            {--queue : Process sync queue}
                            {--model= : Specific model to sync}
                            {--force : Force sync even if offline}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize data with central hub';

    /**
     * Execute the console command.
     */
    public function handle(SyncService $syncService)
    {
        $this->info('Starting data synchronization...');

        try {
            if ($this->option('push')) {
                $this->pushChanges($syncService);
            }

            if ($this->option('pull')) {
                $this->pullChanges($syncService);
            }

            if ($this->option('queue')) {
                $this->processQueue($syncService);
            }

            // If no specific action specified, do both push and pull
            if (!$this->option('push') && !$this->option('pull') && !$this->option('queue')) {
                $this->pushChanges($syncService);
                $this->pullChanges($syncService);
            }

            $this->info('Synchronization completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Synchronization failed: ' . $e->getMessage());
            Log::error('Sync command failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    /**
     * Push local changes to central hub
     */
    protected function pushChanges(SyncService $syncService): void
    {
        $this->info('Pushing local changes...');
        
        $modelType = $this->option('model');
        $results = $syncService->pushChanges($modelType);
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Success', $results['success']],
                ['Failed', $results['failed']],
            ]
        );

        if ($results['failed'] > 0) {
            $this->warn("{$results['failed']} items failed to sync");
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error['model']} (ID: {$error['id']}): {$error['error']}");
            }
        }
    }

    /**
     * Pull changes from central hub
     */
    protected function pullChanges(SyncService $syncService): void
    {
        $this->info('Pulling changes from central hub...');
        
        $results = $syncService->pullChanges();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Success', $results['success']],
                ['Failed', $results['failed']],
            ]
        );

        if ($results['failed'] > 0) {
            $this->warn("{$results['failed']} changes failed to apply");
            foreach ($results['errors'] as $error) {
                $this->line("  - Change: " . json_encode($error['change']));
                $this->line("    Error: {$error['error']}");
            }
        }
    }

    /**
     * Process sync queue
     */
    protected function processQueue(SyncService $syncService): void
    {
        $this->info('Processing sync queue...');
        
        $results = $syncService->processQueue();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $results['processed']],
                ['Failed', $results['failed']],
            ]
        );

        if ($results['failed'] > 0) {
            $this->warn("{$results['failed']} queue items failed");
            foreach ($results['errors'] as $error) {
                $this->line("  - Item ID: {$error['item_id']}");
                $this->line("    Error: {$error['error']}");
            }
        }
    }
} 