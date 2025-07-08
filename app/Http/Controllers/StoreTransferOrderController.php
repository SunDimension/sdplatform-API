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
        $receiveOrders = StoreTransferOrder::where('source_status','outgoing')->where('source_store_id',auth()->user()->store_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','incoming')->where('source_status','approved')->where('destination_store_id',auth()->user()->store_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return response()->json(['data' => [
            'incoming' =>  StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    public function branch_pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status','Pending')->where('source_branch_id',auth()->user()->branch_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','Pending')->where('destination_branch_id',auth()->user()->branch_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        // return new StoreTransferOrderCollection($receiveOrders);
        return response()->json(['data' => [
            'incoming' =>  StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    public function store(StoreTransferOrderStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['source_status'] = 'Pending';
        $validated['destination_status'] = 'Pending';   
        $validated['created_by'] = auth()->user()->id;
        // $validated['transfer_date'] = now();
        $storeTransferOrder = StoreTransferOrder::create($validated);

        foreach ($validated['items'] as $item) {
            StoreTransferItem::create([
                'transfer_order_id' => $storeTransferOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'quantity_pieces' => $item['quantity_pieces'],
                'unit_price' => $item['unit_price'],
                'description' => $item['description'],
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
            'id'=>['required', 'string'],
            'source'=>['required', 'string'],
            'stage'=>['required', 'string']
        ]);

        if($validated['source'] == 'source'){
            $storeTransferOrder = StoreTransferOrder::where('id',$validated['id'])->where('source_status','outgoing')->first();
            if($validated['stage'] == 'store'){
                $storeTransferOrder->source_status = $validated['status'];
                $storeTransferOrder->source_store_approval_by = auth()->user()->id;
                $storeTransferOrder->source_store_approval_date = now();
            }
            if($validated['stage'] == 'branch'){
                $storeTransferOrder = StoreTransferOrder::where('id',$validated['id'])->where('source_status','pending')->first();
                $storeTransferOrder->source_status = $validated['status'];
                $storeTransferOrder->source_branch_approval_by = auth()->user()->id;
                $storeTransferOrder->source_branch_approval_date = now();
            }
            $storeTransferOrder->save();
        }elseif($validated['source'] == 'destination'){

            $storeTransferOrder = StoreTransferOrder::where('id',$validated['id'])->where('destination_status','incoming')->first();
            if($validated['stage'] == 'store'){ 
                $storeTransferOrder->destination_status = $validated['status'];
                $storeTransferOrder->destination_store_approval_by = auth()->user()->id;
                $storeTransferOrder->destination_store_approval_date = now();
                if( $storeTransferOrder->destination_branch_id == $storeTransferOrder->source_branch_id && $validated['status']=='pending'){
                    $storeTransferOrder->destination_status = 'approved';
                }
            }
            if($validated['stage'] == 'branch'){
                $storeTransferOrder = StoreTransferOrder::where('id',$validated['id'])->where('destination_status','pending')->first();
                $storeTransferOrder->destination_status = $validated['status'];
                $storeTransferOrder->destination_branch_approval_by = auth()->user()->id;
                $storeTransferOrder->destination_branch_approval_date = now();
            }
            $storeTransferOrder->save();
        }
        
        return new StoreTransferOrderResource($storeTransferOrder);
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
        $storeTransferOrder->delete();

        return response()->noContent();
    }
}
