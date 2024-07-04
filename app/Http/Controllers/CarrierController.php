<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarrierStoreRequest;
use App\Http\Requests\CarrierUpdateRequest;
use App\Http\Resources\CarrierCollection;
use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarrierController extends Controller
{
    public function index(Request $request): CarrierCollection
    {
        $carriers = Carrier::all();

        return new CarrierCollection($carriers);
    }

    public function store(CarrierStoreRequest $request): CarrierResource
    {
        $carrier = Carrier::create($request->validated());

        return new CarrierResource($carrier);
    }

    public function show(Request $request, Carrier $carrier): CarrierResource
    {
        return new CarrierResource($carrier);
    }

    public function update(CarrierUpdateRequest $request, Carrier $carrier): CarrierResource
    {
        $carrier->update($request->validated());

        return new CarrierResource($carrier);
    }

    public function destroy($id): Response
    {
       Carrier::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
