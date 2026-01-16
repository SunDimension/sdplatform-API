<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use Illuminate\Support\Str;

class FixStoreSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:fix-stores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing Store models by setting proper sync data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing Store models sync data...');
        
        $locationId = config('app.location_id', 'default');
        $this->info("Using location ID: {$locationId}");
        
        $stores = Store::all();
        $this->info("Found {$stores->count()} Store models");
        
        $fixed = 0;
        $skipped = 0;
        
        foreach ($stores as $store) {
            $changes = [];
            
            // Fix sync_id if missing
            if (!$store->sync_id) {
                $store->sync_id = Str::uuid();
                $changes[] = 'sync_id';
            }
            
            // Fix location_id if missing
            if (!$store->location_id) {
                $store->location_id = $locationId;
                $changes[] = 'location_id';
            }
            
            // Fix sync_status if it's 'synced' but shouldn't be
            if ($store->sync_status === 'synced' && !$store->last_synced_at) {
                $store->sync_status = 'pending';
                $changes[] = 'sync_status';
            }
            
            // Fix sync_version if missing
            if (!$store->sync_version) {
                $store->sync_version = 1;
                $changes[] = 'sync_version';
            }
            
            if (!empty($changes)) {
                $store->save();
                $fixed++;
                $this->line("Fixed Store ID {$store->id} ({$store->name}): " . implode(', ', $changes));
            } else {
                $skipped++;
            }
        }
        
        $this->info("\nSync fix completed!");
        $this->info("Fixed: {$fixed} stores");
        $this->info("Skipped: {$skipped} stores");
        
        // Show summary
        $pendingCount = Store::whereIn('sync_status', ['pending', 'deleted_pending'])->count();
        $this->info("Stores now needing sync: {$pendingCount}");
        
        return 0;
    }
}
