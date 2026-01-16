<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveItemStoreRequest;
use App\Http\Requests\ReceiveItemUpdateRequest;
use App\Http\Resources\ReceiveItemCollection;
use App\Http\Resources\ReceiveItemResource;
use App\Models\ReceiveItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiveItemController extends Controller
{
    public function index(Request $request): Response
    {
        $receiveItems = ReceiveItem::all();

        return new ReceiveItemCollection($receiveItems);
    }

    public function store(ReceiveItemStoreRequest $request): Response
    {
        $receiveItem = ReceiveItem::create($request->validated());

        return new ReceiveItemResource($receiveItem);
    }

    public function show(Request $request, ReceiveItem $receiveItem): Response
    {
        return new ReceiveItemResource($receiveItem);
    }

    public function update(ReceiveItemUpdateRequest $request, ReceiveItem $receiveItem): Response
    {
        $receiveItem->update($request->validated());

        return new ReceiveItemResource($receiveItem);
    }

    public function destroy(Request $request, ReceiveItem $receiveItem): Response
    {
        $receiveItem->delete();

        return response()->noContent();
    }
}
