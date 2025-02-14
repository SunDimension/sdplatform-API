<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemCategoryStoreRequest;
use App\Http\Requests\ItemCategoryUpdateRequest;
use App\Http\Resources\ItemCategoryCollection;
use App\Http\Resources\ItemCategoryResource;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemCategoryController extends Controller
{
    public function index(Request $request): ItemCategoryCollection
    {
        $itemCategories = ItemCategory::all();

        return new ItemCategoryCollection($itemCategories);
    }

    public function store(ItemCategoryStoreRequest $request): ItemCategoryResource
    {
        $itemCategory = ItemCategory::create($request->validated());

        return new ItemCategoryResource($itemCategory);
    }

    public function show(Request $request, ItemCategory $itemCategory): ItemCategoryResource
    {
        return new ItemCategoryResource($itemCategory);
    }

    public function update(ItemCategoryUpdateRequest $request, ItemCategory $itemCategory): ItemCategoryResource
    {
        $itemCategory->update($request->validated());

        return new ItemCategoryResource($itemCategory);
    }

    public function destroy($id)
    {   
       
        ItemCategory::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
