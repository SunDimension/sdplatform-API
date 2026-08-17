<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyViewsStoreRequest;
use App\Http\Requests\PropertyViewsUpdateRequest;
use App\Http\Resources\PropertyViewCollection;
use App\Http\Resources\PropertyViewResource;
use App\Models\PropertyView;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyViewsController extends Controller
{
    public function index(Request $request): PropertyViewCollection
    {
        $propertyViews = PropertyView::all();

        return new PropertyViewCollection($propertyViews);
    }

    public function store(PropertyViewsStoreRequest $request): PropertyViewResource
    {
        $propertyView = PropertyView::create($request->validated());

        return new PropertyViewResource($propertyView);
    }

    public function show(Request $request, PropertyView $propertyView): PropertyViewResource
    {
        return new PropertyViewResource($propertyView);
    }

    public function update(PropertyViewsUpdateRequest $request, PropertyView $propertyView): PropertyViewResource
    {
        $propertyView->update($request->validated());

        return new PropertyViewResource($propertyView);
    }

    public function destroy(Request $request, PropertyView $propertyView): Response
    {
        $propertyView->delete();

        return response()->noContent();
    }
}
