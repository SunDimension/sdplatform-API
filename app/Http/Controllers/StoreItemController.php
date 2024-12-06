<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreItemResource;
use App\Http\Resources\StoreItemCollection;
use App\Http\Requests\StoreItemStoreRequest;
use App\Http\Requests\StoreItemUpdateRequest;
use App\Models\Store;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Auth;

class StoreItemController extends Controller
{
  
    public function index(Request $request)
    {
        $storeitem = StoreItem::all();
        return StoreItemResource::collection($storeitem);
    }

    public function GetInventoryByStore($itemId)
    {
        //$storeitem = StoreItem::where("item_id", $item_id)->where;
        $item_ids = Store::where('branch_id', auth()->user()->branch_id)->pluck('id');

        $storeItems = StoreItem::where('create_item_id', $itemId)
            ->whereIn('store_id', $item_ids)
            ->get();
            
        return  StoreItemResource::collection($storeItems);
    }

    public function GetInventoryByStoreBranch($itemId, $branchId)
    {
        //$storeitem = StoreItem::where("item_id", $item_id)->where;
        $item_ids = Store::where('branch_id', $branchId)->pluck('id');

        $storeItems = StoreItem::where('create_item_id', $itemId)
            ->whereIn('store_id', $item_ids)
            ->get();
            
        return  StoreItemResource::collection($storeItems);
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

    public function update(StoreItemUpdateRequest $request,  $id): StoreItemResource
    {   
      Log::debug($request->validated());
      $storeitem = StoreItem::findOrFail($id);
        Log::debug($storeitem);
        
        $storeitem->update($request->validated());

        return new StoreItemResource($storeitem);
    }

   public function destroy($id)
    {   
       
        StoreItem::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
