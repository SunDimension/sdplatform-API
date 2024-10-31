<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferOrderStoreRequest;
use App\Http\Requests\StoreTransferOrderUpdateRequest;
use App\Http\Resources\StoreTransferOrderCollection;
use App\Http\Resources\StoreTransferOrderResource;
use App\Models\StoreTransferOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreTransferOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $storeTransferOrders = StoreTransferOrder::all();

        return new StoreTransferOrderCollection($storeTransferOrders);
    }

    public function store(StoreTransferOrderStoreRequest $request): Response
    {
        $storeTransferOrder = StoreTransferOrder::create($request->validated());

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function show(Request $request, StoreTransferOrder $storeTransferOrder): Response
    {
        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function update(StoreTransferOrderUpdateRequest $request, StoreTransferOrder $storeTransferOrder): Response
    {
        $storeTransferOrder->update($request->validated());

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function destroy(Request $request, StoreTransferOrder $storeTransferOrder): Response
    {
        $storeTransferOrder->delete();

        return response()->noContent();
    }
}
