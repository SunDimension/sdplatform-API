<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertiesStoreRequest;
use App\Http\Requests\PropertiesUpdateRequest;
use App\Http\Resources\PropertyCollection;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertiesController extends Controller
{
    public function index(Request $request): PropertyCollection
    {
        $properties = Property::all();

        return new PropertyCollection($properties);
    }

    public function store(PropertiesStoreRequest $request): PropertyResource
    {
        $property = Property::create($request->validated());

        return new PropertyResource($property);
    }

    public function show(Request $request, Property $property): PropertyResource
    {
        return new PropertyResource($property);
    }

    public function update(PropertiesUpdateRequest $request, Property $property): PropertyResource
    {
        $property->update($request->validated());

        return new PropertyResource($property);
    }

    public function destroy(Request $request, Property $property): Response
    {
        $property->delete();

        return response()->noContent();
    }
}
