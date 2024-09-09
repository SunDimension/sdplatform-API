<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjustmentTypeStoreRequest;
use App\Http\Requests\AdjustmentTypeUpdateRequest;
use App\Http\Resources\AdjustmentTypeCollection;
use App\Http\Resources\AdjustmentTypeResource;
use App\Models\AdjustmentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdjustmentTypeController extends Controller
{
    public function index(Request $request): AdjustmentTypeCollection
    {
        $adjustmentTypes = AdjustmentType::all();

        return new AdjustmentTypeCollection($adjustmentTypes);
    }

    public function store(AdjustmentTypeStoreRequest $request): AdjustmentTypeResource
    {
        $adjustmentType = AdjustmentType::create($request->validated());

        return new AdjustmentTypeResource($adjustmentType);
    }

    public function show(Request $request, AdjustmentType $adjustmentType): AdjustmentTypeResource
    {
        return new AdjustmentTypeResource($adjustmentType);
    }

    public function update(AdjustmentTypeUpdateRequest $request, AdjustmentType $adjustmentType): AdjustmentTypeResource
    {
        $adjustmentType->update($request->validated());

        return new AdjustmentTypeResource($adjustmentType);
    }

    public function destroy($id)
    {
        AdjustmentType::destroy($id);

        return response()->noContent();
    }
}
