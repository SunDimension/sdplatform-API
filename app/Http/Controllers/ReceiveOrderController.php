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
        $receiveOrders = ReceiveOrder::where('status', 'Pending')->where('store_id', auth()->user()->store_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return new ReceiveOrderCollection($receiveOrders);
    }

    public function store(ReceiveOrderStoreRequest $request)
    {
        $validated = $request->validated();
        //Log::debug($validated);

        $receiveOrder = ReceiveOrder::create($validated);
        $itemSoldIds = [];
        foreach ($validated['items'] as $item) {
            //Log::debug($item);

            $unit = Measurement::where('id', $item['unit_measurement'])->first()->name;
            $createItem = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $request->store_id)->first();
            ReceiveItem::create([
                'receive_order_id' => $receiveOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'quantity_pieces' => StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity']),
                'unit_price' => $item['unit_price'],
                'description' => $item['description'],
                'unit_measurement' => $item['unit_measurement'],
                'created_by' => auth()->user()->id
            ]);
        }
        return new ReceiveOrderResource($receiveOrder);
    }

public function approve(Request $request)
{
    $validated = $request->validate([
        'comment' => ['nullable'],
        'status' => ['required', 'string'],
        'id' => ['required', 'string']
    ]);
    
    $receiveOrder = ReceiveOrder::findOrFail($validated['id']);
    $receiveOrder->approval_comment = $validated['comment'];
    $receiveOrder->status = $validated['status'];
    $receiveOrder->approved_by = auth()->user()->id;
    $receiveOrder->approval_date = now();
    $receiveOrder->save();

    if ($receiveOrder->status == 'Approved') {
        foreach ($receiveOrder->receiveItems as $item) {
            // Get the current store item record
            $storeItem = StoreItem::where('create_item_id', $item->product_id)
                ->where('store_id', $receiveOrder->store_id)
                ->first();
            
            // If store item doesn't exist, create it (optional)
            if (!$storeItem) {
                $storeItem = StoreItem::create([
                    'create_item_id' => $item->product_id,
                    'store_id' => $receiveOrder->store_id,
                    'branch_id' => $receiveOrder->branch_id,
                    'quantity' => 0,
                    'created_by' => auth()->id()
                ]);
            }
            
            // Log the audit trail
            ProductAudit::create([
                'action_type' => 'replenished',
                'product_id' => $item->product_id,
                'store_id' => $receiveOrder->store_id,
                'user_id' => auth()->id(),
                'quantity_change' => $item->quantity,
                'previous_quantity' => $storeItem->quantity,
                'new_quantity' => $storeItem->quantity + $item->quantity,
                'reference_type' => 'ReceiveOrder',
                'reference_id' => $receiveOrder->id,
                'notes' => 'Stock replenishment'
            ]);
        }
    }

    return new ReceiveOrderResource($receiveOrder);
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
        $receiveOrder->delete();

        return response()->noContent();
    }
}
