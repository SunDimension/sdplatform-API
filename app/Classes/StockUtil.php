<?php

namespace App\Classes;

use App\Models\ItemSold;
use App\Models\ReceiveItem;
use App\Models\ReleaseDetails;
use App\Models\StoreItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockUtil
{
    /**
     * Get the actual stock quantity for an item in a store.
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getActualQuantity($item_id, $store_id)
    {
        // Get the opening stock quantity
        $storeItem = StoreItem::where('create_item_id', $item_id)
            ->where('store_id', $store_id)
            ->first();

        if (!$storeItem) {
            Log::warning("StoreItem not found for item_id: $item_id and store_id: $store_id");
            return 0;
        }

        $openStock = $storeItem->open_stock;

        // Calculate total received quantity
        $stockReceived = self::getTotalReceivedQuantity($item_id, $store_id);

        // Calculate total released quantity
        $stockReleased = self::getTotalReleasedQuantity($item_id, $store_id);

        // Additional stock adjustments (if needed)
        $stockTransfered = 0; // Example: Transfers to other stores
        $stockReturned = 0;  // Example: Returned items

        return $openStock + $stockReceived - $stockReleased + $stockTransfered + $stockReturned;
    }

    /**
     * Get the available quantity for a request (excluding pending sales).
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getQuantityForRequest($item_id, $store_id)
    {
        // Get the opening stock quantity
        $storeItem = StoreItem::where('create_item_id', $item_id)
            ->where('store_id', $store_id)
            ->first();

        if (!$storeItem) {
            Log::warning("StoreItem not found for item_id: $item_id and store_id: $store_id");
            return 0;
        }

        $openStock = $storeItem->open_stock * $storeItem->quantity_in_package;

        // Calculate total received quantity
        $stockReceived = self::getTotalReceivedQuantity($item_id, $store_id);

        // Calculate total released quantity
        $stockReleased = self::getTotalReleasedQuantity($item_id, $store_id);

        // Calculate pending sales quantity
        $stockSalesPending = self::getTotalPendingSalesQuantity($item_id, $store_id);

        // Additional stock adjustments (if needed)
        $stockTransfered = 0; // Example: Transfers to other stores
        $stockReturned = 0;  // Example: Returned items

        return $openStock + $stockReceived - $stockReleased + $stockTransfered + $stockReturned - $stockSalesPending;
    }

    /**
     * Get the total received quantity for an item in a store.
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    private static function getTotalReceivedQuantity($item_id, $store_id)
    {
        $result = ReceiveItem::query()
            ->join('receive_orders', 'receive_items.receive_order_id', '=', 'receive_orders.id')
            ->select('receive_items.product_id', DB::raw('SUM(receive_items.quantity_pieces) as total_quantity'))
            ->where('receive_items.product_id', $item_id)
            ->where('receive_orders.store_id', $store_id)
            ->groupBy('receive_items.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    /**
     * Get the total released quantity for an item in a store.
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    private static function getTotalReleasedQuantity($item_id, $store_id)
    {
        $result = ReleaseDetails::query()
            ->join('releases', 'release_details.release_id', '=', 'releases.id')
            ->select('release_details.product_id', DB::raw('SUM(release_details.quantity_pieces) as total_quantity'))
            ->where('release_details.product_id', $item_id)
            ->where('releases.store_id', $store_id)
            ->groupBy('release_details.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    /**
     * Get the total pending sales quantity for an item in a store.
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    private static function getTotalPendingSalesQuantity($item_id, $store_id)
    {
        $result = ItemSold::query()
            ->join('sales_orders', 'item_solds.sales_order_id', '=', 'sales_orders.id')
            ->select('item_solds.product_id', DB::raw('SUM(item_solds.quantity_pieces) as total_quantity'))
            ->where('item_solds.product_id', $item_id)
            ->where('item_solds.store_id', $store_id)
            ->where('item_solds.status', 'pending')
            ->whereIn('sales_orders.status', ['Pending','Approved','Paid'])
            ->groupBy('item_solds.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    public static function getPieceQuivalent($unit, $package_size,$quantity)
    {
        if($unit=='full')
            $quantity = $quantity*$package_size;
        elseif($unit=='half')
            $quantity = $quantity*$package_size/2.0;

        return $quantity;
    }
}