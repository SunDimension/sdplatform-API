<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemPriceStoreRequest;
use App\Http\Requests\ItemPriceUpdateRequest;
use App\Http\Resources\ItemPriceCollection;
use App\Http\Resources\ItemPriceResource;
use App\Models\ItemPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemPriceController extends Controller
{
    public function index(Request $request): ItemPriceCollection
    {
        $itemPrices = ItemPrice::all();

        return new ItemPriceCollection($itemPrices);
    }

    public function store(ItemPriceStoreRequest $request): ItemPriceResource
    {
        $itemType = ItemPrice::create($request->validated());

        return new ItemPriceResource($itemType);
    }

    public function show(Request $request, ItemPriceResource $itemType): ItemPriceResource
    {
        return new ItemPriceResource($itemType);
    }

    public function update(ItemPriceUpdateRequest $request, ItemPrice $itemPrice): ItemPriceResource
    {
        $itemPrice->update($request->validated());

        return new ItemPriceResource($itemPrice);
    }

    public function destroy($id)
    {   
       
        ItemPrice::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
