<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Http\Resources\ManufacturerCollection;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ManufacturerController extends Controller
{
    public function index(Request $request): ManufacturerCollection
    {
        $manufacturers = Manufacturer::all();

        return new ManufacturerCollection($manufacturers);
    }

    public function store(ManufacturerStoreRequest $request): ManufacturerResource
    {
        $manufacturer = Manufacturer::create($request->validated());

        return new ManufacturerResource($manufacturer);
    }

    public function show(Request $request, Manufacturer $manufacturer): ManufacturerResource
    {
        return new ManufacturerResource($manufacturer);
    }

    public function update(ManufacturerUpdateRequest $request, Manufacturer $manufacturer): ManufacturerResource
    {
        $manufacturer->update($request->validated());

        return new ManufacturerResource($manufacturer);
    }

    public function destroy(Request $request, Manufacturer $manufacturer): Response
    {
        $manufacturer->delete();

        return response()->noContent();
    }
}
