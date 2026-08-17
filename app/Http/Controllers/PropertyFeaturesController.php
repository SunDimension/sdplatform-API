<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyFeaturesStoreRequest;
use App\Http\Requests\PropertyFeaturesUpdateRequest;
use App\Http\Resources\PropertyFeatureCollection;
use App\Http\Resources\PropertyFeatureResource;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyFeaturesController extends Controller
{
    public function index(Request $request): PropertyFeatureCollection
    {
        $propertyFeatures = PropertyFeature::all();

        return new PropertyFeatureCollection($propertyFeatures);
    }

    public function store(PropertyFeaturesStoreRequest $request): PropertyFeatureResource
    {
        $propertyFeature = PropertyFeature::create($request->validated());

        return new PropertyFeatureResource($propertyFeature);
    }

    public function show(Request $request, PropertyFeature $propertyFeature): PropertyFeatureResource
    {
        return new PropertyFeatureResource($propertyFeature);
    }

    public function update(PropertyFeaturesUpdateRequest $request, PropertyFeature $propertyFeature): PropertyFeatureResource
    {
        $propertyFeature->update($request->validated());

        return new PropertyFeatureResource($propertyFeature);
    }

    public function destroy(Request $request, PropertyFeature $propertyFeature): Response
    {
        $propertyFeature->delete();

        return response()->noContent();
    }
}
