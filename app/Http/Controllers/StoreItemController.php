<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreItemResource;
use App\Http\Resources\StoreItemCollection;
use App\Http\Requests\StoreItemStoreRequest;
use App\Http\Requests\StoreItemUpdateRequest;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreItemController extends Controller
{
  
    public function index(Request $request): StoreItemCollection
    {
        $storeitem = StoreItem::all();

        return new StoreItemCollection($storeitem);
    }
    public function store(StoreItemStoreRequest $request): StoreItemResource
    {
        $storeitem = StoreItem::create($request->validated());

        return new StoreItemResource($storeitem);
    }

    public function show(Request $request, StoreItem $storeitem): StoreItemResource
    {
        return new StoreItemResource($storeitem);
    }

    public function update(StoreItemUpdateRequest $request, StoreItem $storeitem): StoreItemResource
    {
        $storeitem->update($request->validated());

        return new StoreItemResource($storeitem);
    }

   public function destroy($id)
    {   
       
        StoreItem::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
