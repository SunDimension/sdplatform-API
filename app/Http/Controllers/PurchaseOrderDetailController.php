<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderDetailStoreRequest;
use App\Http\Requests\PurchaseOrderDetailUpdateRequest;
use App\Http\Resources\PurchaseOrderDetailCollection;
use App\Http\Resources\PurchaseOrderDetailResource;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchaseOrderDetailController extends Controller
{
    public function index(Request $request): PurchaseOrderDetailCollection
    {
        $purchaseOrderDetails = PurchaseOrderDetail::all();

        return new PurchaseOrderDetailCollection($purchaseOrderDetails);
    }

    public function store(PurchaseOrderDetailStoreRequest $request): PurchaseOrderDetailResource
    {
        $purchaseOrderDetail = PurchaseOrderDetail::create($request->validated());

        return new PurchaseOrderDetailResource($purchaseOrderDetail);
    }

    public function show(Request $request, PurchaseOrderDetail $purchaseOrderDetail): PurchaseOrderDetailResource
    {
        return new PurchaseOrderDetailResource($purchaseOrderDetail);
    }

    public function update(PurchaseOrderDetailUpdateRequest $request, PurchaseOrderDetail $purchaseOrderDetail): PurchaseOrderDetailResource
    {
        $purchaseOrderDetail->update($request->validated());

        return new PurchaseOrderDetailResource($purchaseOrderDetail);
    }

   public function destroy($id): Response
    {   
       
        PurchaseOrderDetail::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
