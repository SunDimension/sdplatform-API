<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Http\Resources\StockMovementCollection;
use App\Models\StockMovement;
use App\Models\PurchaseItemCost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'from_date'   => 'nullable|date',
            'to_date'     => 'nullable|date',
            'product_id'  => 'nullable|string|exists:purchase_item_costs,id', // This is the purchase_item_cost id
            'create_item_id' => 'nullable|string|exists:create_items,id', // If you want to filter by the actual product
        ]);

        $fromDate   = $validated['from_date'] ?? null;
        $toDate     = $validated['to_date'] ?? null;
        $productId  = $validated['product_id'] ?? null; // purchase_item_cost id
        $createItemId = $validated['create_item_id'] ?? null; // create_item id

        // Base query with eager loading
        // Load product (PurchaseItemCost) and then the actual item (CreateItem)
        $query = StockMovement::with([
            'product.product', // This loads PurchaseItemCost and then CreateItem
            'createdByUser',
            'approvedByUser',
        ]);

        // Apply direct product filter (filter by purchase_item_cost id)
        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Apply create_item filter (filter by the actual product/item from create_items table)
        if ($createItemId) {
            $query->whereHas('product', function ($q) use ($createItemId) {
                $q->where('product_id', $createItemId);
            });
        }

        // === DATE RANGE FILTER ===
        if ($fromDate || $toDate) {
            // Convert dates to Carbon instances only if provided
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            // Apply date filter using movement_date (not created_at)
            if ($fromDate && $toDate) {
                // Both from_date and to_date are provided
                $query->whereBetween('movement_date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                // Only from_date is provided
                $query->where('movement_date', '>=', $fromDate);
            } elseif ($toDate) {
                // Only to_date is provided
                $query->where('movement_date', '<=', $toDate);
            }

            // Optional: Filter by branch if you have branch_id in stock_movements table
            // $user = auth()->user();
            // if ($user && isset($user->branch_id)) {
            //     $query->where('branch_id', $user->branch_id);
            // }
        }

        // Order by most recent movement first
        $query->orderBy('movement_date', 'desc');

        // Execute query
        $stockMovements = $query->get();

        // Debugging log
        Log::info('Stock Movements Filtered', [
            'from_date' => $fromDate?->toDateTimeString(),
            'to_date'   => $toDate?->toDateTimeString(),
            'product_id' => $productId,
            'create_item_id' => $createItemId,
            'count'     => $stockMovements->count(),
            'dates'     => $stockMovements->pluck('movement_date')->take(10)->toArray(),
        ]);

        return new StockMovementCollection($stockMovements);
    }

    public function search(Request $request)
    {
        // Same exact code as index()
        return $this->index($request);
    }
}
