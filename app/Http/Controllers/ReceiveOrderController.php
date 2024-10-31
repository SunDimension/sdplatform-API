<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveOrderStoreRequest;
use App\Http\Requests\ReceiveOrderUpdateRequest;
use App\Http\Resources\ReceiveOrderCollection;
use App\Http\Resources\ReceiveOrderResource;
use App\Models\ReceiveOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiveOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $receiveOrders = ReceiveOrder::all();

        return new ReceiveOrderCollection($receiveOrders);
    }

    public function store(ReceiveOrderStoreRequest $request): Response
    {
        $receiveOrder = ReceiveOrder::create($request->validated());

        return new ReceiveOrderResource($receiveOrder);
    }

    public function show(Request $request, ReceiveOrder $receiveOrder): Response
    {
        return new ReceiveOrderResource($receiveOrder);
    }

    public function update(ReceiveOrderUpdateRequest $request, ReceiveOrder $receiveOrder): Response
    {
        $receiveOrder->update($request->validated());

        return new ReceiveOrderResource($receiveOrder);
    }

    public function destroy(Request $request, ReceiveOrder $receiveOrder): Response
    {
        $receiveOrder->delete();

        return response()->noContent();
    }
}
