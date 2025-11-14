<?php

namespace App\Observers;

use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;

class SalesOrderObserver
{
    /**
     * Handle the SalesOrder "created" event.
     */
    public function created(SalesOrder $salesOrder): void
    {
        // Mark related ItemSold records for sync
        $this->syncRelatedItems($salesOrder);
    }

    /**
     * Handle the SalesOrder "updated" event.
     */
    public function updated(SalesOrder $salesOrder): void
    {
        // Mark related ItemSold records for sync
        $this->syncRelatedItems($salesOrder);
    }

    /**
     * Mark related ItemSold records for sync
     */
    protected function syncRelatedItems(SalesOrder $salesOrder): void
    {
        try {
            // Get all ItemSold records for this sales order
            $itemsSold = $salesOrder->itemSold()->get();

            foreach ($itemsSold as $itemSold) {
                // Mark each ItemSold for sync if it's not already synced
                if ($itemSold->sync_status !== 'synced') {
                    $itemSold->markForSync();
                }
            }

            Log::info('Related ItemSold records marked for sync', [
                'sales_order_id' => $salesOrder->id,
                'items_count' => $itemsSold->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark related ItemSold records for sync', [
                'sales_order_id' => $salesOrder->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
