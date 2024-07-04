<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartCategoryStoreRequest;
use App\Http\Requests\ChartCategoryUpdateRequest;
use App\Http\Resources\ChartCategoryCollection;
use App\Http\Resources\ChartCategoryResource;
use App\Models\ChartCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $chartCategories = ChartCategory::all();

        return new ChartCategoryCollection($chartCategories);
    }

    public function store(ChartCategoryStoreRequest $request): Response
    {
        $chartCategory = ChartCategory::create($request->validated());

        return new ChartCategoryResource($chartCategory);
    }

    public function show(Request $request, ChartCategory $chartCategory): Response
    {
        return new ChartCategoryResource($chartCategory);
    }

    public function update(ChartCategoryUpdateRequest $request, ChartCategory $chartCategory): Response
    {
        $chartCategory->update($request->validated());

        return new ChartCategoryResource($chartCategory);
    }

    public function destroy(Request $request, ChartCategory $chartCategory): Response
    {
        $chartCategory->delete();

        return response()->noContent();
    }
}
