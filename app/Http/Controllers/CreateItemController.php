<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStoreRequest;
use App\Http\Requests\CreateItemUpdateRequest;
use App\Http\Resources\CreateItemCollection;
use App\Http\Resources\CreateItemResource;
use App\Models\CreateItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateItemController extends Controller
{
    public function index(Request $request): CreateItemCollection
    {
        $createItems = CreateItem::all();

        return new CreateItemCollection($createItems);
    }

    public function store(CreateItemStoreRequest $request): CreateItemResource
    {
        $createItem = CreateItem::create($request->validated());

        return new CreateItemResource($createItem);
    }

    public function show(Request $request, CreateItem $createItem): CreateItemResource
    {
        return new CreateItemResource($createItem);
    }

    public function update(CreateItemUpdateRequest $request, CreateItem $createItem): CreateItemResource
    {
        $createItem->update($request->validated());

        return new CreateItemResource($createItem);
    }

    public function destroy($id): Response
    {
        CreateItem::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
