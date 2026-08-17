<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyTypesStoreRequest;
use App\Http\Requests\PropertyTypesUpdateRequest;
use App\Http\Resources\PropertyTypeCollection;
use App\Http\Resources\PropertyTypeResource;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyTypesController extends Controller
{
    public function index(Request $request): PropertyTypeCollection
    {
        $propertyTypes = PropertyType::all();

        return new PropertyTypeCollection($propertyTypes);
    }

    public function store(PropertyTypesStoreRequest $request): PropertyTypeResource
    {
        $propertyType = PropertyType::create($request->validated());

        return new PropertyTypeResource($propertyType);
    }

    public function show(Request $request, PropertyType $propertyType): PropertyTypeResource
    {
        return new PropertyTypeResource($propertyType);
    }

    public function update(PropertyTypesUpdateRequest $request, PropertyType $propertyType): PropertyTypeResource
    {
        $propertyType->update($request->validated());

        return new PropertyTypeResource($propertyType);
    }

    public function destroy(Request $request, PropertyType $propertyType): Response
    {
        $propertyType->delete();

        return response()->noContent();
    }
}
