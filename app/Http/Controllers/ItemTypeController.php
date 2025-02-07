<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemTypeStoreRequest;
use App\Http\Requests\ItemTypeUpdateRequest;
use App\Http\Resources\ItemTypeCollection;
use App\Http\Resources\ItemTypeResource;
use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemTypeController extends Controller
{
    public function index(Request $request): ItemTypeCollection
    {
        $itemTypes = ItemType::all();

        return new ItemTypeCollection($itemTypes);
    }

    public function store(ItemTypeStoreRequest $request): ItemTypeResource
    {
        $itemType = ItemType::create($request->validated());

        return new ItemTypeResource($itemType);
    }

    public function show(Request $request, ItemType $itemType): ItemTypeResource
    {
        return new ItemTypeResource($itemType);
    }

    public function update(ItemTypeUpdateRequest $request, ItemType $itemType): ItemTypeResource
    {
        $itemType->update($request->validated());

        return new ItemTypeResource($itemType);
    }

    public function destroy($id)
    {   
       
        ItemType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
