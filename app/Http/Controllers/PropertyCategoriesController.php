<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyCategoriesStoreRequest;
use App\Http\Requests\PropertyCategoriesUpdateRequest;
use App\Http\Resources\PropertyCategoryCollection;
use App\Http\Resources\PropertyCategoryResource;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyCategoriesController extends Controller
{
    public function index(Request $request): PropertyCategoryCollection
    {
        $propertyCategories = PropertyCategory::all();

        return new PropertyCategoryCollection($propertyCategories);
    }

    public function store(PropertyCategoriesStoreRequest $request): PropertyCategoryResource
    {
        $propertyCategory = PropertyCategory::create($request->validated());

        return new PropertyCategoryResource($propertyCategory);
    }

    public function show(Request $request, PropertyCategory $propertyCategory): PropertyCategoryResource
    {
        return new PropertyCategoryResource($propertyCategory);
    }

    public function update(PropertyCategoriesUpdateRequest $request, PropertyCategory $propertyCategory): PropertyCategoryResource
    {
        $propertyCategory->update($request->validated());

        return new PropertyCategoryResource($propertyCategory);
    }

    public function destroy(Request $request, PropertyCategory $propertyCategory): Response
    {
        $propertyCategory->delete();

        return response()->noContent();
    }
}
