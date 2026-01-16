<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartTypeStoreRequest;
use App\Http\Requests\ChartTypeUpdateRequest;
use App\Http\Resources\ChartTypeCollection;
use App\Http\Resources\ChartTypeResource;
use App\Models\ChartType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartTypeController extends Controller
{
    public function index(Request $request)
    {
        $chartTypes = ChartType::all();

        return new ChartTypeCollection($chartTypes);
    }

    public function store(ChartTypeStoreRequest $request)
    {
        $chartType = ChartType::create($request->validated());

        return new ChartTypeResource($chartType);
    }

    public function show(Request $request, ChartType $chartType)
    {
        return new ChartTypeResource($chartType);
    }

    public function update(ChartTypeUpdateRequest $request, ChartType $chartType)
    {
        $chartType->update($request->validated());

        return new ChartTypeResource($chartType);
    }

    public function destroy(Request $request, ChartType $chartType)
    {
        $chartType->delete();

        return response()->noContent();
    }
}
