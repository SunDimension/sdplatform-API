<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderStoreRequest;
use App\Http\Requests\PurchaseOrderUpdateRequest;
use App\Http\Resources\PurchaseOrderCollection;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{

    public function index(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|string|exists:suppliers,id',
            'from_date'   => 'nullable|date',
            'to_date'     => 'nullable|date',
            'product_id'  => 'nullable|string|exists:purchase_item_costs,id',
            'status'      => 'nullable|string|in:sent,received,completed,canceled',
        ]);

        $supplierId = $validated['supplier_id'] ?? null;
        $fromDate   = $validated['from_date'] ?? null;
        $toDate     = $validated['to_date'] ?? null;
        $productId  = $validated['product_id'] ?? null;
        $status     = $validated['status'] ?? null;

        // Base query with eager loading
        $query = PurchaseOrder::with([
            'supplier',
            'createdByUser',
            'approvedByUser',
            'purchaseOrderItems',
            'purchaseOrderItems.product',
            'purchaseOrderItems.product.product'
        ]);

        // Apply supplier filter
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Apply product filter (via items)
        if ($productId) {
            $query->whereHas('purchaseOrderItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // === DATE RANGE FILTER - MIRRORED FROM WORKING CODE ===
        if ($fromDate || $toDate) {
            // Convert from_date and to_date to Carbon instances only if provided
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            // Apply date filter for the selected range along with the branch condition
            if ($fromDate && $toDate) {
                // Both from_date and to_date are provided
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                // Only from_date is provided
                $query->where('created_at', '>=', $fromDate);
            } elseif ($toDate) {
                // Only to_date is provided
                $query->where('created_at', '<=', $toDate);
            }

            // Ensure transactions are fetched only for the user's branch when filtering by date
            // $user = auth()->user(); // Get the logged-in user
            // $query->where('branch_id', $user->branch_id); // Filter by branch_id (user's branch)
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc', 'status=>sent');

        // Execute query
        $purchaseOrders = $query->get();

        // Optional: Keep this for debugging during testing
        Log::info('Purchase Orders Filtered', [
            'from_date' => $fromDate,
            'to_date'   => $toDate,
            'count'     => $purchaseOrders->count(),
            'dates'     => $purchaseOrders->pluck('order_date')->take(10)->toArray(),
        ]);

        return new PurchaseOrderCollection($purchaseOrders);
    }

    // ... rest of your existing methods remain the same



    public function store(PurchaseOrderStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create the purchase order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => $request->status ?? 'sent',
                'created_by' => $request->user_id ?? auth()->id(),
                'approved_by' => $request->approved_by,
            ]);

            // Create purchase order items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    PurchaseOrderItem::create([
                        'po_id' => $purchaseOrder->po_id,
                        'product_id' => $item['product_id'],
                        'quantity_ordered' => $item['quantity_ordered'],
                        'unit_price' => $item['unit_price'],
                         'sync_status' => 'pending',
                        // 'amount' => $item['amount'],
                        // 'created_by' => $request->user_id ?? auth()->id(),
                    ]);
                }
            }

            DB::commit();

            // Load relationships for response


            return new PurchaseOrderResource($purchaseOrder);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create purchase order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        return new PurchaseOrderResource(
            $purchaseOrder->load([
                'purchaseOrderItems.product.product', // Nested loading
                'supplier',
                'createdByUser',
                'approvedByUser'
            ])
        );
    }

    public function update(PurchaseOrderUpdateRequest $request, PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        try {
            DB::beginTransaction();

            $purchaseOrder->update($request->validated());

            // Update items if provided
            if ($request->has('items') && is_array($request->items)) {
                // Delete existing items
                $purchaseOrder->purchaseOrderItems()->delete();

                // Create new items
                foreach ($request->items as $item) {
                    PurchaseOrderItem::create([
                        'po_id' => $purchaseOrder->po_id,
                        'product_id' => $item['product_id'],
                        'quantity_ordered' => $item['quantity_ordered'],
                        'unit_price' => $item['unit_price'],
                        'amount' => $item['amount'],
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return new PurchaseOrderResource(
                $purchaseOrder->load([
                    'purchaseOrderItems.product',
                    'supplier',
                    'createdByUser',
                    'approvedByUser'
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update purchase order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Delete associated items first
            $purchaseOrder->purchaseOrderItems()->delete();

            // Delete the purchase order
            $purchaseOrder->delete();

            DB::commit();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order deletion failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete purchase order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchPurchase(Request $request)
    {
    
        $validated = $request->validate([
            'supplier_id' => 'nullable|string|exists:suppliers,id',
            'from_date'   => 'nullable|date',
            'to_date'     => 'nullable|date',
            'product_id'  => 'nullable|string|exists:purchase_item_costs,id',
            'status'      => 'nullable|string|in:sent,received,completed,canceled',
        ]);

        $supplierId = $validated['supplier_id'] ?? null;
        $fromDate   = $validated['from_date'] ?? null;
        $toDate     = $validated['to_date'] ?? null;
        $productId  = $validated['product_id'] ?? null;
        $status     = $validated['status'] ?? null;

        // Base query with eager loading
        $query = PurchaseOrder::with([
            'supplier',
            'createdByUser',
            'approvedByUser',
            'purchaseOrderItems',
            'purchaseOrderItems.product',
            'purchaseOrderItems.product.product'
        ]);

        // Apply supplier filter
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Apply product filter (via items)
        if ($productId) {
            $query->whereHas('purchaseOrderItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // === DATE RANGE FILTER - MIRRORED FROM WORKING CODE ===
        if ($fromDate || $toDate) {
            // Convert from_date and to_date to Carbon instances only if provided
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            // Apply date filter for the selected range along with the branch condition
            if ($fromDate && $toDate) {
                // Both from_date and to_date are provided
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                // Only from_date is provided
                $query->where('created_at', '>=', $fromDate);
            } elseif ($toDate) {
                // Only to_date is provided
                $query->where('created_at', '<=', $toDate);
            }

            // Ensure transactions are fetched only for the user's branch when filtering by date
            // $user = auth()->user(); // Get the logged-in user
            // $query->where('branch_id', $user->branch_id); // Filter by branch_id (user's branch)
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc', 'status=>sent');

        // Execute query
        $purchaseOrders = $query->get();

        // Optional: Keep this for debugging during testing
        Log::info('Purchase Orders Filtered', [
            'from_date' => $fromDate,
            'to_date'   => $toDate,
            'count'     => $purchaseOrders->count(),
            'dates'     => $purchaseOrders->pluck('order_date')->take(10)->toArray(),
        ]);

        return new PurchaseOrderCollection($purchaseOrders);
    
    }
}
