<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPurchaseOrderStoreRequest;
use App\Http\Requests\NewPurchaseOrderUpdateRequest;
use App\Http\Resources\NewPurchaseOrderCollection;
use App\Http\Resources\NewPurchaseOrderResource;
use App\Models\NewPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewPurchaseOrderController extends Controller
{
    public function index(Request $request): NewPurchaseOrderCollection
    {
        $newPurchaseOrders = NewPurchaseOrder::all();

        return new NewPurchaseOrderCollection($newPurchaseOrders);
    }

    public function store(NewPurchaseOrderStoreRequest $request): NewPurchaseOrderResource
    {
        $newPurchaseOrder = NewPurchaseOrder::create($request->validated());

        return new NewPurchaseOrderResource($newPurchaseOrder);
    }

    public function show(Request $request, NewPurchaseOrder $newPurchaseOrder): NewPurchaseOrderResource
    {
        return new NewPurchaseOrderResource($newPurchaseOrder);
    }

    public function update(NewPurchaseOrderUpdateRequest $request, NewPurchaseOrder $newPurchaseOrder): NewPurchaseOrderResource
    {
        $newPurchaseOrder->update($request->validated());

        return new NewPurchaseOrderResource($newPurchaseOrder);
    }

    public function destroy($id): Response
    {   
       
        NewPurchaseOrder::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
