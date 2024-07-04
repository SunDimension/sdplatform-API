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
    public function index(Request $request): Response
    {
        $chartTypes = ChartType::all();

        return new ChartTypeCollection($chartTypes);
    }

    public function store(ChartTypeStoreRequest $request): Response
    {
        $chartType = ChartType::create($request->validated());

        return new ChartTypeResource($chartType);
    }

    public function show(Request $request, ChartType $chartType): Response
    {
        return new ChartTypeResource($chartType);
    }

    public function update(ChartTypeUpdateRequest $request, ChartType $chartType): Response
    {
        $chartType->update($request->validated());

        return new ChartTypeResource($chartType);
    }

    public function destroy(Request $request, ChartType $chartType): Response
    {
        $chartType->delete();

        return response()->noContent();
    }
}
