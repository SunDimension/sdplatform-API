<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreItemResource;
use App\Http\Resources\StoreItemResource2;
use App\Http\Resources\StoreItemCollection;
use App\Http\Requests\StoreItemStoreRequest;
use App\Http\Requests\StoreItemUpdateRequest;
use App\Http\Resources\CreateItemResource;
use App\Models\CreateItem;
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
    $query = StoreItem::with('createItem') // 👈 Eager-load createItem
        ->where('branch_id', auth()->user()->branch_id)
        ->get();

    return StoreItemResource::collection($query);
}


    public function myStoreItems()
    {
        $item_ids = Store::where('branch_id', auth()->user()->branch_id)->pluck('id');
        $storeItems = StoreItem::whereIn('store_id', $item_ids)->get()->pluck('create_item_id');
        //$createIds = StoreItem::where('branch_id', auth()->user()->branch_id)->pluck('create_item_id');
        Log::alert("dada",[$storeItems]);
        $item = CreateItem::whereIn('id',$storeItems)->get();
        return  CreateItemResource::collection($item);
    }

     public function myStoreItemsSetLimit()
    {
        $item_ids = Store::where('branch_id', auth()->user()->branch_id)->pluck('id');
        $storeItems = StoreItem::whereIn('store_id', $item_ids)->get();
        //$createIds = StoreItem::where('branch_id', auth()->user()->branch_id)->pluck('create_item_id');
        Log::alert("dada",[$storeItems]);
        // $item = CreateItem::whereIn('id',$storeItems)->get();
        return  StoreItemResource2::collection($storeItems);
    }

public function setLimit(Request $request, $id)
{
    // Validate the request data
    $validated = $request->validate([
        'set_limit' => ['nullable', 'numeric', 'min:0'], // Ensure set_limit is numeric
        'id' => ['required', 'integer', 'exists:store_items,id'] // Ensure store item exists
    ]);

    // Find the store item by ID
    $storeItem = StoreItem::findOrFail($validated["id"]);

    // Update the set_limit (allow null to remove it)
    $storeItem->set_limit = $validated['set_limit'] !== null ? (int)$validated['set_limit'] : null;
    $storeItem->save();

    // Return a success response with the updated store item data
    return response()->json([
        'message' => $storeItem->set_limit === null 
            ? 'Set limit removed successfully' 
            : 'Set limit successfully updated',
        'storeItem' => new StoreItemResource($storeItem),
    ], Response::HTTP_OK);
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
