<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferOrderStoreRequest;
use App\Http\Requests\TransferOrderUpdateRequest;
use App\Http\Resources\TransferOrderCollection;
use App\Http\Resources\TransferOrderResource;
use App\Models\TransferOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferOrderController extends Controller
{
    public function index(Request $request): TransferOrderCollection
    {
        $transferOrders = TransferOrder::all();

        return new TransferOrderCollection($transferOrders);
    }

    public function store(TransferOrderStoreRequest $request): TransferOrderResource
    {
        $transferOrder = TransferOrder::create($request->validated());

        return new TransferOrderResource($transferOrder);
    }

    public function show(Request $request, TransferOrder $transferOrder): TransferOrderResource
    {
        return new TransferOrderResource($transferOrder);
    }

    public function update(TransferOrderUpdateRequest $request, TransferOrder $transferOrder): TransferOrderResource
    {
        $transferOrder->update($request->validated());

        return new TransferOrderResource($transferOrder);
    }

 public function destroy($id): Response
    {   
       
        TransferOrder::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
