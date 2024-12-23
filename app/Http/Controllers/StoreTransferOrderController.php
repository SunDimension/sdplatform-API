<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferOrderStoreRequest;
use App\Http\Requests\StoreTransferOrderUpdateRequest;
use App\Http\Resources\StoreTransferOrderCollection;
use App\Http\Resources\StoreTransferOrderResource;
use App\Models\StoreTransferItem;
use App\Models\StoreTransferOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StoreTransferOrderController extends Controller
{
    public function index(Request $request)
    {
        $storeTransferOrders = StoreTransferOrder::all();

        return new StoreTransferOrderCollection($storeTransferOrders);
    }

    public function pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status','Pending')->where('store_id',auth()->user()->store_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','Pending')->where('store_id',auth()->user()->store_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return new StoreTransferOrderCollection($receiveOrders);
    }

    public function branch_pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status','Pending')->where('branch_id',auth()->user()->branch_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','Pending')->where('branch_id',auth()->user()->branch_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return new StoreTransferOrderCollection($receiveOrders);
    }

    public function store(StoreTransferOrderStoreRequest $request)
    {
        $validated = $request->validated();
        $storeTransferOrder = StoreTransferOrder::create($validated);
        
        foreach ($validated['items'] as $item) {
            StoreTransferItem::create([
                'transfer_order_id' => $storeTransferOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                // 'description' => $item['description'],
                'created_by' => auth()->user()->id
            ]);
        }

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id'=>['required', 'string']
        ]);
        // $receiveOrder = StoreTransferOrder::findOrFail($validated['id']);
        // $receiveOrder->approval_comment = $validated['comment'];
        // $receiveOrder->status = $validated['status'];
        // $receiveOrder->approved_by = auth()->user()->id;
        // $receiveOrder->approval_date = now();
        // $receiveOrder->save();

        // if($receiveOrder->status == 'Approved')
        // {
        //     $sql = "UPDATE store_items si INNER JOIN (
        //             SELECT sum(quantity) quantity, product_id, avg(quantity*unit_price)/sum(quantity) unit_price, ro.store_id  
        //             from receive_items ri INNER JOIN receive_orders ro ON ro.id = ri.receive_order_id
        //             WHERE receive_order_id = ?
        //             group BY product_id) A ON si.store_id = A.store_id  and si.create_item_id = A.product_id
        //             SET si.quantity = si.quantity+A.quantity,
        //             si.cost_price = A.unit_price ";

        //     DB::update($sql, [$validated['id']]);
        // }

        return new ReceiveOrderResource($receiveOrder);
    }

    public function show(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function update(StoreTransferOrderUpdateRequest $request, StoreTransferOrder $storeTransferOrder)
    {
        $storeTransferOrder->update($request->validated());

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function destroy(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        $storeTransferOrder->delete();

        return response()->noContent();
    }
}
