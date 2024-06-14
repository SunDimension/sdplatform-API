<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryStoreRequest;
use App\Http\Requests\DeliveryUpdateRequest;
use App\Http\Resources\DeliveryCollection;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeliveryController extends Controller
{
    public function index(Request $request): DeliveryCollection
    {
        $deliveries = Delivery::all();

        return new DeliveryCollection($deliveries);
    }

    public function store(DeliveryStoreRequest $request): DeliveryResource
    {
        $delivery = Delivery::create($request->validated());

        return new DeliveryResource($delivery);
    }

    public function show(Request $request, Delivery $delivery): DeliveryResource
    {
        return new DeliveryResource($delivery);
    }

    public function update(DeliveryUpdateRequest $request, Delivery $delivery): DeliveryResource
    {
        $delivery->update($request->validated());

        return new DeliveryResource($delivery);
    }

    public function destroy(Request $request, Delivery $delivery): Response
    {
        $delivery->delete();

        return response()->noContent();
    }
}
