<?php

namespace App\Http\Controllers;

use App\Classes\StockUtil;
use App\Http\Requests\ReceiveOrderStoreRequest;
use App\Http\Requests\ReceiveOrderUpdateRequest;
use App\Http\Resources\ReceiveOrderCollection;
use App\Http\Resources\ReceiveOrderResource;
use App\Models\Measurement;
use App\Models\ReceiveItem;
use App\Models\ProductAudit;
use App\Models\ReceiveOrder;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceiveOrderController extends Controller
{
    public function index(Request $request)
    {
        $receiveOrders = ReceiveOrder::where('status', 'Approved')
            ->get();
        return new ReceiveOrderCollection($receiveOrders);
    }

    public function pending(Request $request)
    {
        $receiveOrders = ReceiveOrder::where('status', 'Pending')
            ->where('store_id', auth()->user()->store_id)
            ->get();
        return new ReceiveOrderCollection($receiveOrders);
    }

    public function store(ReceiveOrderStoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $receiveOrder = ReceiveOrder::create($validated);

            foreach ($validated['items'] as $item) {
                $unit = Measurement::where('id', $item['unit_measurement'])->first()->name;
                $createItem = StoreItem::where('create_item_id', $item['product_id'])
                    ->where('store_id', $request->store_id)
                    ->first();

                // Create receive item record
                $receiveItem = ReceiveItem::create([
                    'receive_order_id' => $receiveOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_pieces' => StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity']),
                    'unit_price' => $item['unit_price'],
                    'description' => $item['description'],
                    'unit_measurement' => $item['unit_measurement'],
                    'created_by' => auth()->user()->id
                ]);

                // --- ProductAudit for receive order creation (pending approval) ---
                $previousQuantity = StockUtil::getQuantityForRequest($item['product_id'], $request->store_id);
                $quantityChange = StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity']);
                $newQuantity = $previousQuantity + $quantityChange;

                ProductAudit::create([
                    'action_type' => 'receive_pending',
                    'product_id' => $item['product_id'],
                    'user_id' => auth()->id(),
                    'store_id' => $request->store_id,
                    'quantity_change' => $quantityChange,
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => 'ReceiveOrder',
                    'reference_id' => $receiveOrder->id,
                    'notes' => 'Receive order created - pending approval'
                ]);
                // --- End ProductAudit ---
            }

            DB::commit();
            return new ReceiveOrderResource($receiveOrder);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Receive order creation failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create receive order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required', 'string']
        ]);

        DB::beginTransaction();
        try {
            $receiveOrder = ReceiveOrder::findOrFail($validated['id']);
            $receiveOrder->approval_comment = $validated['comment'];
            $receiveOrder->status = $validated['status'];
            $receiveOrder->approved_by = auth()->user()->id;
            $receiveOrder->approval_date = now();
            $receiveOrder->save();

            if ($receiveOrder->status == 'Approved') {
                foreach ($receiveOrder->receiveItems as $item) {
                    $storeItem = StoreItem::where('create_item_id', $item->product_id)
                        ->where('store_id', $receiveOrder->store_id)
                        ->first();

                    if (!$storeItem) {
                        $storeItem = StoreItem::create([
                            'create_item_id' => $item->product_id,
                            'store_id' => $receiveOrder->store_id,
                            'branch_id' => $receiveOrder->branch_id,
                            'quantity' => 0,
                            'created_by' => auth()->id()
                        ]);
                    }

                    // Calculate quantities before update

                }
            }

            DB::commit();
            return new ReceiveOrderResource($receiveOrder);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Receive order approval failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to approve receive order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, ReceiveOrder $receiveOrder)
    {
        return new ReceiveOrderResource($receiveOrder->load('receiveItems'));
    }

    public function update(ReceiveOrderUpdateRequest $request, ReceiveOrder $receiveOrder)
    {
        $receiveOrder->update($request->validated());
        return new ReceiveOrderResource($receiveOrder);
    }

    public function destroy(Request $request, ReceiveOrder $receiveOrder)
    {
        DB::beginTransaction();
        try {
            // Log audit records before deletion
            foreach ($receiveOrder->receiveItems as $item) {
                $storeItem = StoreItem::where('create_item_id', $item->product_id)
                    ->where('store_id', $receiveOrder->store_id)
                    ->first();

                ProductAudit::create([
                    'action_type' => ProductAudit::ACTION_RECEIPT_CANCELLED,
                    'product_id' => $item->product_id,
                    'store_id' => $receiveOrder->store_id,
                    'user_id' => auth()->id(),
                    'quantity_change' => -$item->quantity_pieces, // Negative for cancelled receipt
                    'previous_quantity' => $storeItem ? $storeItem->quantity : 0,
                    'new_quantity' => $storeItem ? $storeItem->quantity : 0,
                    'reference_type' => 'ReceiveOrder',
                    'reference_id' => $receiveOrder->id,
                    'notes' => 'Receive order cancelled - stock not received'
                ]);
            }

            $receiveOrder->delete();
            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Receive order deletion failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete receive order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
