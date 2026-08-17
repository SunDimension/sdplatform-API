<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavedPropertiesStoreRequest;
use App\Http\Requests\SavedPropertiesUpdateRequest;
use App\Http\Resources\SavedPropertyCollection;
use App\Http\Resources\SavedPropertyResource;
use App\Models\SavedProperty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SavedPropertiesController extends Controller
{
    public function index(Request $request): SavedPropertyCollection
    {
        $savedProperties = SavedProperty::all();

        return new SavedPropertyCollection($savedProperties);
    }

    public function store(SavedPropertiesStoreRequest $request): SavedPropertyResource
    {
        $savedProperty = SavedProperty::create($request->validated());

        return new SavedPropertyResource($savedProperty);
    }

    public function show(Request $request, SavedProperty $savedProperty): SavedPropertyResource
    {
        return new SavedPropertyResource($savedProperty);
    }

    public function update(SavedPropertiesUpdateRequest $request, SavedProperty $savedProperty): SavedPropertyResource
    {
        $savedProperty->update($request->validated());

        return new SavedPropertyResource($savedProperty);
    }

    public function destroy(Request $request, SavedProperty $savedProperty): Response
    {
        $savedProperty->delete();

        return response()->noContent();
    }
}
