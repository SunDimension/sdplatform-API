<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStatusesStoreRequest;
use App\Http\Requests\PropertyStatusesUpdateRequest;
use App\Http\Resources\PropertyStatusCollection;
use App\Http\Resources\PropertyStatusResource;
use App\Models\PropertyStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyStatusesController extends Controller
{
    public function index(Request $request): PropertyStatusCollection
    {
        $propertyStatuses = PropertyStatus::all();

        return new PropertyStatusCollection($propertyStatuses);
    }

    public function store(PropertyStatusesStoreRequest $request): PropertyStatusResource
    {
        $propertyStatus = PropertyStatus::create($request->validated());

        return new PropertyStatusResource($propertyStatus);
    }

    public function show(Request $request, PropertyStatus $propertyStatus): PropertyStatusResource
    {
        return new PropertyStatusResource($propertyStatus);
    }

    public function update(PropertyStatusesUpdateRequest $request, PropertyStatus $propertyStatus): PropertyStatusResource
    {
        $propertyStatus->update($request->validated());

        return new PropertyStatusResource($propertyStatus);
    }

    public function destroy(Request $request, PropertyStatus $propertyStatus): Response
    {
        $propertyStatus->delete();

        return response()->noContent();
    }
}
