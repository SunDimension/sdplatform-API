<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnItemStoreRequest;
use App\Http\Requests\ReturnItemUpdateRequest;
use App\Http\Resources\ReturnItemCollection;
use App\Http\Resources\ReturnItemResource;
use App\Models\PostInflow;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReturnItemController extends Controller
{
    public function index(Request $request): ReturnItemCollection
    {
        $returnItem = ReturnItem::all();

        return new ReturnItemCollection($returnItem);
    }
    public function store(ReturnItemStoreRequest $request): ReturnItemResource
    {
        $returnItem = ReturnItem::create($request->validated());

        return new ReturnItemResource($returnItem);
    }

    public function show(Request $request, ReturnItem $returnItem): ReturnItemResource
    {
        return new ReturnItemResource($returnItem);
    }

    public function update(ReturnItemUpdateRequest $request, ReturnItem $returnItem): ReturnItemResource
    {
        $returnItem->update($request->validated());

        return new ReturnItemResource($returnItem);
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id'=>['required']
        ]);

        
        $returnItem = ReturnItem::findOrFail($validated['id']);
        $returnItem->approval_comment = $validated['comment'];
        $returnItem->return_status = $validated['return_status'];
        $returnItem->approved_by = auth()->user()->id;
        $returnItem->approval_date = now();
        $returnItem->save();

        if($validated['return_status'] == 'approved'){
            $data = new PostInflow();
            $data->inflow_status = 3; // Unclaimed status
            $data->customer_id = $returnItem->customer_id;
            $data->amount = $returnItem->returnDetails->sum(function($detail) {
                return $detail->return_quantity * $detail->unit_price;
            });
            $data->narration = "Return of items";
            $data->inflow_date = now();
            $data->save();
            
        }


        return new ReturnItemResource($returnItem);
    }

   public function destroy($id)
    {   
       
        ReturnItem::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
