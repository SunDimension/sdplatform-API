<?php

namespace App\Classes;

use App\Models\ItemSold;
use App\Models\ReceiveItem;
use App\Models\ReleaseDetails;
use App\Models\ReturnDetails;
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
        $stockTransfered = self::getTotalReceivedViaTransferQuantity($item_id, $store_id) - self::getTotalTransferredQuantityRequest($item_id, $store_id); // Example: Transfers to other stores
        $stockReturned = self::getTotalReturnQuantity($item_id, $store_id);  // Example: Returned items

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
        $stockTransfered = self::getTotalReceivedViaTransferQuantity($item_id, $store_id) - self::getTotalTransferredQuantityRequest($item_id, $store_id); // Example: Transfers to other stores
        $stockReturned = self::getTotalReturnQuantity($item_id, $store_id);  // Example: Returned items

        return $openStock + $stockReceived - $stockReleased + $stockTransfered + $stockReturned - $stockSalesPending;
    }

    public static function getQuantityActual($item_id, $store_id)
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
        //$stockSalesPending = self::getTotalPendingSalesQuantity($item_id, $store_id);

        // Additional stock adjustments (if needed)
        $stockTransfered = 0; // Example: Transfers to other stores
        $stockReturned = self::getTotalReturnQuantity($item_id, $store_id);  // Example: Returned items

        return $openStock + $stockReceived - $stockReleased + $stockTransfered + $stockReturned;
    }

    /**
     * Get the total transferred quantity for an item from a store (outgoing transfers).
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getTotalTransferredQuantity($item_id, $store_id)
    {
        // Outgoing transfers: items sent from this store to another store
        $result = \App\Models\StoreTransferItem::query()
            ->join('store_transfer_orders', 'store_transfer_items.transfer_order_id', '=', 'store_transfer_orders.id')
            ->select('store_transfer_items.product_id', \DB::raw('SUM(store_transfer_items.quantity_pieces) as total_quantity'))
            ->where('store_transfer_items.product_id', $item_id)
            ->where('store_transfer_orders.source_store_id', $store_id)
            ->whereIn('store_transfer_orders.source_status', ['approved']) // Only count approved/outgoing
            ->groupBy('store_transfer_items.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    /**
     * Get the total received via transfer quantity for an item into a store (incoming transfers).
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getTotalReceivedViaTransferQuantity($item_id, $store_id)
    {
        // Incoming transfers: items received into this store from another store
        $result = \App\Models\StoreTransferItem::query()
            ->join('store_transfer_orders', 'store_transfer_items.transfer_order_id', '=', 'store_transfer_orders.id')
            ->select('store_transfer_items.product_id', \DB::raw('SUM(store_transfer_items.quantity_pieces) as total_quantity'))
            ->where('store_transfer_items.product_id', $item_id)
            ->where('store_transfer_orders.destination_store_id', $store_id)
            ->whereIn('store_transfer_orders.destination_status', ['approved']) // Only count approved/incoming
            ->groupBy('store_transfer_items.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    /**
     * Get the total transferred quantity for an item from a store (outgoing transfers).
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getTotalTransferredQuantityRequest($item_id, $store_id)
    {
        // Outgoing transfers: items sent from this store to another store
        $result = \App\Models\StoreTransferItem::query()
            ->join('store_transfer_orders', 'store_transfer_items.transfer_order_id', '=', 'store_transfer_orders.id')
            ->select('store_transfer_items.product_id', \DB::raw('SUM(store_transfer_items.quantity_pieces) as total_quantity'))
            ->where('store_transfer_items.product_id', $item_id)
            ->where('store_transfer_orders.source_store_id', $store_id)
            ->whereIn('store_transfer_orders.source_status', ['approved', 'outgoing','pending']) // Only count approved/outgoing
            ->groupBy('store_transfer_items.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    /**
     * Get the total received via transfer quantity for an item into a store (incoming transfers).
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    public static function getTotalReceivedViaTransferQuantityRequest($item_id, $store_id)
    {
        // Incoming transfers: items received into this store from another store
        $result = \App\Models\StoreTransferItem::query()
            ->join('store_transfer_orders', 'store_transfer_items.transfer_order_id', '=', 'store_transfer_orders.id')
            ->select('store_transfer_items.product_id', \DB::raw('SUM(store_transfer_items.quantity_pieces) as total_quantity'))
            ->where('store_transfer_items.product_id', $item_id)
            ->where('store_transfer_orders.destination_store_id', $store_id)
            ->whereIn('store_transfer_orders.destination_status', ['approved', 'incoming','pending']) // Only count approved/incoming
            ->groupBy('store_transfer_items.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }
    public static function getQuantityActual($item_id, $store_id)
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
        //$stockSalesPending = self::getTotalPendingSalesQuantity($item_id, $store_id);

        // Additional stock adjustments (if needed)
        $stockTransfered = 0; // Example: Transfers to other stores
        $stockReturned = self::getTotalReturnQuantity($item_id, $store_id);  // Example: Returned items

        return $openStock + $stockReceived - $stockReleased + $stockTransfered + $stockReturned ;
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
     * Get the total released quantity for an item in a store.
     *
     * @param int $item_id
     * @param int $store_id
     * @return float
     */
    private static function getTotalReturnQuantity($item_id, $store_id)
    {
        $result = ReturnDetails::query()
            ->join('return_items', 'return_details.return_id', '=', 'return_items.id')
            ->select('return_details.product_id', DB::raw('SUM(return_details.return_quantity_pieces) as total_quantity'))
            ->where('return_details.product_id', $item_id)
            ->where('return_items.store_id', $store_id)
            ->where('return_items.return_status', 'Approved')
            ->groupBy('return_details.product_id')
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
            ->whereIn('sales_orders.status', ['Pending', 'Approved', 'Paid'])
            ->groupBy('item_solds.product_id')
            ->first();

        return $result ? $result->total_quantity : 0;
    }

    public static function getPieceQuivalent($unit, $package_size, $quantity)
    {
        // Log::alert($unit);

        if ($unit == 'Full') {
            $quantity = $quantity * $package_size;
            //Log::alert('f');
        } elseif ($unit == 'Half') {
            // Log::alert('h');
            $quantity = $quantity * $package_size / 2.0;
        }

        return $quantity;
    }
    public static function getPieceEquivalentForRequest($unit, $package_size, $quantity)
    {
        if ($unit == 'Full') {
            $quantity = $quantity * $package_size;
        } elseif ($unit == 'Half') {
            $quantity = $quantity * $package_size / 2.0;
        }

        return $quantity;
    }
}
