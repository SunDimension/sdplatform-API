<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyAmenitiesStoreRequest;
use App\Http\Requests\PropertyAmenitiesUpdateRequest;
use App\Http\Resources\PropertyAmenityCollection;
use App\Http\Resources\PropertyAmenityResource;
use App\Models\PropertyAmenity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyAmenitiesController extends Controller
{
    public function index(Request $request): PropertyAmenityCollection
    {
        $propertyAmenities = PropertyAmenity::all();

        return new PropertyAmenityCollection($propertyAmenities);
    }

    public function store(PropertyAmenitiesStoreRequest $request): PropertyAmenityResource
    {
        $propertyAmenity = PropertyAmenity::create($request->validated());

        return new PropertyAmenityResource($propertyAmenity);
    }

    public function show(Request $request, PropertyAmenity $propertyAmenity): PropertyAmenityResource
    {
        return new PropertyAmenityResource($propertyAmenity);
    }

    public function update(PropertyAmenitiesUpdateRequest $request, PropertyAmenity $propertyAmenity): PropertyAmenityResource
    {
        $propertyAmenity->update($request->validated());

        return new PropertyAmenityResource($propertyAmenity);
    }

    public function destroy(Request $request, PropertyAmenity $propertyAmenity): Response
    {
        $propertyAmenity->delete();

        return response()->noContent();
    }
}
