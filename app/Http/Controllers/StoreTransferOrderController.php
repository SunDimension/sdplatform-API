<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferOrderStoreRequest;
use App\Http\Requests\StoreTransferOrderUpdateRequest;
use App\Http\Resources\StoreTransferOrderCollection;
use App\Http\Resources\StoreTransferOrderResource;
use App\Models\ProductAudit;
use App\Models\StoreItem;
use App\Models\StoreTransferItem;
use App\Classes\StockUtil;
use App\Models\StoreTransferOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreTransferOrderController extends Controller
{
    public function index(Request $request)
    {
        $storeTransferOrders = StoreTransferOrder::all();
        return new StoreTransferOrderCollection($storeTransferOrders);
    }

    public function pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status', 'outgoing')
            ->where('source_store_id', auth()->user()->store_id)
            ->get();

        $receiveOrders2 = StoreTransferOrder::where('destination_status', 'incoming')
            ->where('source_status', 'approved')
            ->where('destination_store_id', auth()->user()->store_id)
            ->get();

        return response()->json(['data' => [
            'incoming' => StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    public function branch_pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status', 'Pending')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->get();

        $receiveOrders2 = StoreTransferOrder::where('destination_status', 'Pending')
            ->where('destination_branch_id', auth()->user()->branch_id)
            ->get();

        return response()->json(['data' => [
            'incoming' => StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    /**
     * Log a product audit for store transfer actions using StockUtil logic.
     *
     * @param string $actionType
     * @param int $productId
     * @param int $storeId
     * @param int $quantityChange
     * @param int $referenceId
     * @param string $referenceType
     * @param string $notes
     * @param float $previousQuantity
     * @param float $newQuantity
     * @return void
     */
    private function logTransferAudit($actionType, $productId, $storeId, $quantityChange, $referenceId, $referenceType, $notes, $previousQuantity, $newQuantity)
    {
        ProductAudit::create([
            'action_type' => $actionType,
            'product_id' => $productId,
            'user_id' => auth()->id(),
            'store_id' => $storeId,
            'quantity_change' => $quantityChange,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes
        ]);
    }

    public function store(StoreTransferOrderStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['source_status'] = 'outgoing';
        $validated['destination_status'] = 'incoming';
        $validated['created_by'] = auth()->user()->id;

        DB::beginTransaction();
        try {
            $storeTransferOrder = StoreTransferOrder::create($validated);

            foreach ($validated['items'] as $item) {
                $transferItem = StoreTransferItem::create([
                    'transfer_order_id' => $storeTransferOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_pieces' => $item['quantity_pieces'],
                    'unit_price' => $item['unit_price'],
                    'description' => $item['description'],
                    'created_by' => auth()->user()->id
                ]);

                // Pending transfer audit (source)
                $prevQtySource = StockUtil::getQuantityForRequest($item['product_id'], $validated['source_store_id']);
                $this->logTransferAudit(
                    ProductAudit::ACTION_TRANSFER_OUT_PENDING,
                    $item['product_id'],
                    $validated['source_store_id'],
                    -$item['quantity_pieces'],
                    $storeTransferOrder->id,
                    'StoreTransferOrder',
                    'Pending transfer out to store ' . $validated['destination_store_id'],
                    $prevQtySource,
                    $prevQtySource
                );

                // Pending transfer audit (destination)
                $prevQtyDest = StockUtil::getQuantityForRequest($item['product_id'], $validated['destination_store_id']);
                $this->logTransferAudit(
                    ProductAudit::ACTION_TRANSFER_IN_PENDING,
                    $item['product_id'],
                    $validated['destination_store_id'],
                    $item['quantity_pieces'],
                    $storeTransferOrder->id,
                    'StoreTransferOrder',
                    'Pending transfer in from store ' . $validated['source_store_id'],
                    $prevQtyDest,
                    $prevQtyDest
                );
            }

            DB::commit();
            return new StoreTransferOrderResource($storeTransferOrder);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store transfer order creation failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create transfer order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required', 'string'],
            'source' => ['required', 'string'],
            'stage' => ['required', 'string']
        ]);

        DB::beginTransaction();
        try {
            if ($validated['source'] == 'source') {
                $storeTransferOrder = StoreTransferOrder::where('id', $validated['id'])
                    ->where('source_status', $validated['stage'] == 'store' ? 'outgoing' : 'pending')
                    ->firstOrFail();

                if ($validated['stage'] == 'store') {
                    $storeTransferOrder->source_status = $validated['status'];
                    $storeTransferOrder->source_store_approval_by = auth()->user()->id;
                    $storeTransferOrder->source_store_approval_date = now();
                } elseif ($validated['stage'] == 'branch') {
                    $storeTransferOrder->source_status = $validated['status'];
                    $storeTransferOrder->source_branch_approval_by = auth()->user()->id;
                    $storeTransferOrder->source_branch_approval_date = now();
                }

                // If this is final source approval, update source store inventory
                if ($validated['status'] == 'approved' && $validated['stage'] == 'store') {
                    foreach ($storeTransferOrder->items as $item) {
                        $sourceStoreItem = StoreItem::firstOrCreate([
                            'create_item_id' => $item->product_id,
                            'store_id' => $storeTransferOrder->source_store_id
                        ]);

                        $prevQty = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->source_store_id);
                        $sourceStoreItem->save();
                        $newQty = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->source_store_id);
                        $this->logTransferAudit(
                            ProductAudit::ACTION_TRANSFER_OUT,
                            $item->product_id,
                            $storeTransferOrder->source_store_id,
                            -$item->quantity_pieces,
                            $storeTransferOrder->id,
                            'StoreTransferOrder',
                            'Inventory deducted for transfer to store ' . $storeTransferOrder->destination_store_id,
                            $prevQty,
                            $newQty
                        );
                    }
                }
            } elseif ($validated['source'] == 'destination') {
                $storeTransferOrder = StoreTransferOrder::where('id', $validated['id'])
                    ->where('destination_status', $validated['stage'] == 'store' ? 'incoming' : 'pending')
                    ->firstOrFail();

                if ($validated['stage'] == 'store') {
                    $storeTransferOrder->destination_status = $validated['status'];
                    $storeTransferOrder->destination_store_approval_by = auth()->user()->id;
                    $storeTransferOrder->destination_store_approval_date = now();

                    if (
                        $storeTransferOrder->destination_branch_id == $storeTransferOrder->source_branch_id &&
                        $validated['status'] == 'pending'
                    ) {
                        $storeTransferOrder->destination_status = 'approved';
                    }
                } elseif ($validated['stage'] == 'branch') {
                    $storeTransferOrder->destination_status = $validated['status'];
                    $storeTransferOrder->destination_branch_approval_by = auth()->user()->id;
                    $storeTransferOrder->destination_branch_approval_date = now();
                }

                // If this is final destination approval, update destination store inventory
                if ($storeTransferOrder->destination_status == 'approved' && $validated['stage'] == 'store') {
                    foreach ($storeTransferOrder->items as $item) {
                        $destStoreItem = StoreItem::firstOrCreate([
                            'create_item_id' => $item->product_id,
                            'store_id' => $storeTransferOrder->destination_store_id
                        ]);

                        $prevQty = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->destination_store_id);
                        $destStoreItem->save();
                        $newQty = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->destination_store_id);
                        $this->logTransferAudit(
                            ProductAudit::ACTION_TRANSFER_IN,
                            $item->product_id,
                            $storeTransferOrder->destination_store_id,
                            $item->quantity_pieces,
                            $storeTransferOrder->id,
                            'StoreTransferOrder',
                            'Inventory received from store ' . $storeTransferOrder->source_store_id,
                            $prevQty,
                            $newQty
                        );
                    }
                }
            }

            $storeTransferOrder->save();
            DB::commit();

            return new StoreTransferOrderResource($storeTransferOrder);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store transfer approval failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to approve transfer order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        return new StoreTransferOrderResource($storeTransferOrder->load('items'));
    }

    public function update(StoreTransferOrderUpdateRequest $request, StoreTransferOrder $storeTransferOrder)
    {
        $storeTransferOrder->update($request->validated());
        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function destroy(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        DB::beginTransaction();
        try {
            // Log audit records for cancellation
            foreach ($storeTransferOrder->items as $item) {
                // Source store audit
                $prevQtySource = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->source_store_id);
                $this->logTransferAudit(
                    ProductAudit::ACTION_TRANSFER_CANCELLED,
                    $item->product_id,
                    $storeTransferOrder->source_store_id,
                    0,
                    $storeTransferOrder->id,
                    'StoreTransferOrder',
                    'Transfer cancelled - source store',
                    $prevQtySource,
                    $prevQtySource
                );

                // Destination store audit
                $prevQtyDest = StockUtil::getQuantityForRequest($item->product_id, $storeTransferOrder->destination_store_id);
                $this->logTransferAudit(
                    ProductAudit::ACTION_TRANSFER_CANCELLED,
                    $item->product_id,
                    $storeTransferOrder->destination_store_id,
                    0,
                    $storeTransferOrder->id,
                    'StoreTransferOrder',
                    'Transfer cancelled - destination store',
                    $prevQtyDest,
                    $prevQtyDest
                );
            }

            $storeTransferOrder->delete();
            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store transfer deletion failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete transfer order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
