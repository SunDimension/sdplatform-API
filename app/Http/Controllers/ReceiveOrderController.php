<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveOrderStoreRequest;
use App\Http\Requests\ReceiveOrderUpdateRequest;
use App\Http\Resources\ReceiveOrderCollection;
use App\Http\Resources\ReceiveOrderResource;
use App\Models\ReceiveItem;
use App\Models\ReceiveOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReceiveOrderController extends Controller
{
    public function index(Request $request)
    {
        $receiveOrders = ReceiveOrder::all();
        return new ReceiveOrderCollection($receiveOrders);
    }

    public function pending(Request $request)
    {
        $receiveOrders = ReceiveOrder::where('status','Pending')->where('store_id',auth()->user()->store_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return new ReceiveOrderCollection($receiveOrders);
    }

    public function store(ReceiveOrderStoreRequest $request)
    {
        $validated = $request->validated();
        
            // $unit = Measurement::where('id', $item['unit_measurement'])
                // ->first()->name;
        $receiveOrder = ReceiveOrder::create($validated);
        $itemSoldIds = [];
        foreach ($validated['items'] as $item) {
        
            ReceiveItem::create([
                'receive_order_id' => $receiveOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
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
            'id'=>['required', 'string']
        ]);
        $receiveOrder = ReceiveOrder::findOrFail($validated['id']);
        $receiveOrder->approval_comment = $validated['comment'];
        $receiveOrder->status = $validated['status'];
        $receiveOrder->approved_by = auth()->user()->id;
        $receiveOrder->approval_date = now();
        $receiveOrder->save();

        if($receiveOrder->status == 'Approved')
        {
            $sql = "UPDATE store_items si INNER JOIN (
                    SELECT sum(quantity) quantity, product_id, avg(quantity*unit_price)/sum(quantity) unit_price, ro.store_id  
                    from receive_items ri INNER JOIN receive_orders ro ON ro.id = ri.receive_order_id
                    WHERE receive_order_id = ?
                    group BY product_id) A ON si.store_id = A.store_id  and si.create_item_id = A.product_id
                    SET si.quantity = si.quantity+A.quantity,
                    si.cost_price = A.unit_price ";

            DB::update($sql, [$validated['id']]);
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
