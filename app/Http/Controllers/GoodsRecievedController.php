<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoodsRecievedStoreRequest;
use App\Http\Resources\GoodsRecievedCollection;
use App\Http\Resources\GoodsReceivedResource;
use App\Models\GoodsRecieved;
use App\Models\GoodsRecievedItem;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoodsRecievedController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'nullable|string|exists:purchase_orders,po_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,partial,completed,canceled',
            'only_available' => 'nullable|boolean', // New parameter to filter only available batches
        ]);

        $poId = $validated['po_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $status = $validated['status'] ?? null;
        $onlyAvailable = $validated['only_available'] ?? false;

        $query = GoodsRecieved::with([
            'purchaseOrder',
            'purchaseOrderItems.product',
            'purchaseOrder.supplier',
            'recievedByUser',
            'items' => function ($query) use ($onlyAvailable) {
                if ($onlyAvailable) {
                    // Only load items that are NOT depleted
                    $query->where(function ($q) {
                        $q->where('is_depleted', false)
                            ->orWhereNull('is_depleted');
                    })
                        ->whereRaw('(quantity_received - quantity_damaged) > 0');
                }
            },
            'items.purchaseOrderItem',
            'items.product',
            'items.product.product',
        ])
            ->when($poId, function ($query, $poId) {
                return $query->where('po_id', $poId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            });

        // Handle date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('received_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('received_date', '>=', Carbon::parse($fromDate)->startOfDay());
        } elseif ($toDate) {
            $query->where('received_date', '<=', Carbon::parse($toDate)->endOfDay());
        }

        // If filtering for only available items, exclude GRs with no available items
        if ($onlyAvailable) {
            $query->whereHas('items', function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('is_depleted', false)
                        ->orWhereNull('is_depleted');
                })
                    ->whereRaw('(quantity_received - quantity_damaged) > 0');
            });
        }

        $query->orderBy('recieved_date', 'desc');

        $goodsReceived = $query->get();

        return new GoodsRecievedCollection($goodsReceived);
    }

    /**
     * New endpoint specifically for fetching available items for disbursement
     */
    public function getAvailableItems(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|string',
            'batch_number' => 'nullable|string',
        ]);

        $query = GoodsRecievedItem::with([
            'product',
            'product.product',
            'purchaseOrderItem',
            'goodRecieved',
        ])
            ->where(function ($q) {
                $q->where('is_depleted', false)
                    ->orWhereNull('is_depleted');
            })
            ->whereRaw('(quantity_received - quantity_damaged) > 0')
            ->when($validated['product_id'] ?? null, function ($q, $productId) {
                return $q->where('product_id', $productId);
            })
            ->when($validated['batch_number'] ?? null, function ($q, $batchNumber) {
                return $q->where('batch_number', $batchNumber);
            })
            ->orderBy('expiry_date', 'asc'); // FIFO

        $items = $query->get()->map(function ($item) {
            return [
                'gr_item_id' => $item->gr_item_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product->name ?? 'Unknown',
                'batch_number' => $item->batch_number,
                'expiry_date' => $item->expiry_date,
                'quantity_received' => $item->quantity_received,
                'quantity_damaged' => $item->quantity_damaged,
                'available_quantity' => $item->quantity_received - $item->quantity_damaged,
                'unit_price' => $item->purchaseOrderItem->unit_price ?? 0,
            ];
        });

        return response()->json(['data' => $items]);
    }

    public function store(GoodsRecievedStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::with('purchaseOrderItems.product')
                ->findOrFail($request->po_id);

            if ($purchaseOrder->status !== 'sent') {
                throw new \Exception('Purchase Order is not in "sent" status and cannot be marked as received.');
            }

            $receivedDate = $request->received_date ?? $request->recieved_date ?? now()->toDateString();

            $goodsReceived = GoodsRecieved::create([
                'po_id' => $request->po_id,
                'recieved_date' => $receivedDate,
                'status' => $request->status ?? 'pending',
                'received_by' => $request->recieved_by ?? auth()->id(),
                'remarks' => $request->remarks,
            ]);

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    $poItem = PurchaseOrderItem::where('po_item_id', $itemData['po_item_id'])
                        ->where('po_id', $request->po_id)
                        ->firstOrFail();

                    $grItem = GoodsRecievedItem::create([
                        'gr_id' => $goodsReceived->gr_id,
                        'po_item_id' => $itemData['po_item_id'],
                        'invoice_status' => $request->invoice_status ?? 'not_invoiced',
                        'product_id' => $poItem->product_id,
                        'quantity_received' => $itemData['quantity_received'],
                        'quantity_damaged' => $itemData['quantity_damaged'] ?? 0,
                        'expiry_date' => $itemData['expiry_date'] ?? null,
                        'batch_number' => $itemData['batch_number'] ?? null,
                        'is_depleted' => false, // Initialize as not depleted
                    ]);

                    $usableQuantity = $itemData['quantity_received'] - ($itemData['quantity_damaged'] ?? 0);

                    if ($usableQuantity > 0) {
                        StockMovement::create([
                            'product_id' => $poItem->product_id,
                            'reference_type' => 'goods_received',
                            'reference_id' => $grItem->gr_item_id,
                            'quantity_in' => $usableQuantity,
                            'quantity_out' => 0,
                            'movement_date' => $receivedDate,
                            'unit_cost' => $poItem->unit_price,
                            'status' => 'completed',
                            'created_by' => $request->recieved_by ?? auth()->id(),
                        ]);
                    }
                }
            }

            $purchaseOrder->update(['status' => 'received']);

            DB::commit();

            $goodsReceived->load([
                'purchaseOrder',
                'purchaseOrder.supplier',
                'recievedByUser',
            ]);

            return new GoodsReceivedResource($goodsReceived);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Goods Received creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create goods received',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function items(GoodsRecieved $goodsReceived)
    {
        $goodsReceived->load('items.product');

        return response()->json([
            'data' => $goodsReceived->items
        ]);
    }

    public function show($id)
    {
        $goodsReceived = GoodsRecieved::with([
            'purchaseOrder.supplier',
            'recievedByUser',
            'approvedByUser',
            'items.purchaseOrderItem',
            'items.product.product'
        ])->findOrFail($id);

        return new GoodsReceivedResource($goodsReceived);
    }

    public function update(Request $request, GoodsRecieved $goodsReceived)
    {
        $validated = $request->validate([
            'recieved_date' => 'nullable|date',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.po_item_id' => 'required|string|exists:purchase_order_items,po_item_id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.quantity_damaged' => 'nullable|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $goodsReceived->update([
                'recieved_date' => $validated['recieved_date'] ?? $goodsReceived->recieved_date,
                'status' => $validated['status'] ?? $goodsReceived->status,
                'remarks' => $validated['remarks'] ?? $goodsReceived->remarks,
            ]);

            DB::commit();

            return new GoodsReceivedResource(
                $goodsReceived->load([
                    'purchaseOrder',
                    'purchaseOrder.supplier',
                    'recievedByUser',
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Goods Received update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update goods received',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $goodsReceived = GoodsRecieved::findOrFail($id);

            $grItems = GoodsRecievedItem::where('gr_id', $goodsReceived->gr_id)->get();

            foreach ($grItems as $grItem) {
                StockMovement::where('reference_type', 'goods_received')
                    ->where('reference_id', $grItem->gr_item_id)
                    ->delete();
            }

            GoodsRecievedItem::where('gr_id', $goodsReceived->gr_id)->delete();

            $goodsReceived->delete();

            DB::commit();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Goods Received deletion failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete goods received',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function SearchGoodsReceived(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'nullable|string|exists:purchase_orders,po_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,partial,completed,canceled',
        ]);

        $poId = $validated['po_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $status = $validated['status'] ?? null;

        $query = GoodsRecieved::with([
            'purchaseOrder',
            'purchaseOrderItems.product',
            'purchaseOrder.supplier',
            'recievedByUser',
            'items',
            'items.purchaseOrderItem',
            'items.product',
            'items.product.product',
        ])
            ->when($poId, function ($query, $poId) {
                return $query->where('po_id', $poId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            });

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        } elseif ($toDate) {
            $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
        }

        $query->orderBy('created_at', 'desc');

        $goodsReceived = $query->get();

        return new GoodsRecievedCollection($goodsReceived);
    }
}
