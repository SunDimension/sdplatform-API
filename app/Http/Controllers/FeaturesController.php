<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeaturesStoreRequest;
use App\Http\Requests\FeaturesUpdateRequest;
use App\Http\Resources\FeatureCollection;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeaturesController extends Controller
{
    public function index(Request $request): FeatureCollection
    {
        $features = Feature::all();

        return new FeatureCollection($features);
    }

    public function store(FeaturesStoreRequest $request): FeatureResource
    {
        $feature = Feature::create($request->validated());

        return new FeatureResource($feature);
    }

    public function show(Request $request, Feature $feature): FeatureResource
    {
        return new FeatureResource($feature);
    }

    public function update(FeaturesUpdateRequest $request, Feature $feature): FeatureResource
    {
        $feature->update($request->validated());

        return new FeatureResource($feature);
    }

    public function destroy(Request $request, Feature $feature): Response
    {
        $feature->delete();

        return response()->noContent();
    }
}
