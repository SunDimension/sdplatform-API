<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferItemStoreRequest;
use App\Http\Requests\StoreTransferItemUpdateRequest;
use App\Http\Resources\StoreTransferItemCollection;
use App\Http\Resources\StoreTransferItemResource;
use App\Models\StoreTransferItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class StoreTransferItemController extends Controller
{
    public function index(Request $request): StoreTransferItemCollection
    {
        $storeTransferItems = StoreTransferItem::all();

        return new StoreTransferItemCollection($storeTransferItems);
    }

    public function store(StoreTransferItemStoreRequest $request): StoreTransferItemResource
    {
        $storeTransferItem = StoreTransferItem::create($request->validated());

        return new StoreTransferItemResource($storeTransferItem);
    }

    public function show(Request $request, StoreTransferItem $storeTransferItem): StoreTransferItemResource
    {
        return new StoreTransferItemResource($storeTransferItem);
    }

    public function update(StoreTransferItemUpdateRequest $request, StoreTransferItem $storeTransferItem): StoreTransferItemResource
    {
        $storeTransferItem->update($request->validated());

        return new StoreTransferItemResource($storeTransferItem);
    }

    public function destroy(Request $request, StoreTransferItem $storeTransferItem): Response
    {
        $storeTransferItem->delete();

        return response()->noContent();
    }
}
