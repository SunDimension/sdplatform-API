<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseStoreRequest;
use App\Http\Requests\ReleaseUpdateRequest;
use App\Http\Resources\ReleaseCollection;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use App\Models\CreateItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReleaseController extends Controller
{
    
    public function index(Request $request): ReleaseCollection
    {
        $release = Release::all();

        return new ReleaseCollection($release);
    }
 public function store(ReleaseStoreRequest $request): ReleaseResource
    {
        // Validate input (make sure you get both the create_item_id and quantity to be released)
        $validated = $request->validated();
        $createItem = CreateItem::findOrFail($validated['create_item_id']);

        // Check if there is enough stock
        if ($createItem->quantity < $validated['quantity_released']) {
            return response()->json(['error' => 'Insufficient stock'], Response::HTTP_BAD_REQUEST);
        }

        // Reduce stock from CreateItem (inventory)
        $createItem->quantity -= $validated['quantity_released'];
        $createItem->save();

        // Record the release in the Release table
       $release = Release::create([
    'sales_receipt_id'   => $validated['sales_receipt_id'],
    'branch_id'          => $validated['branch_id'],
    'store_id'           => $validated['store_id'],
    'customer_id'        => $validated['customer_id'],
    'create_item_id'     => $createItem->id, // From inventory
    'quantity_released'  => $validated['quantity_released'],
    'release_date'       => now(),
    'user_id'            => Auth::id(), // The user who authorized the release
]);


        return new ReleaseResource($release);
    }

    public function show(Request $request, Release $release): ReleaseResource
    {
        return new ReleaseResource($release);
    }

    public function update(ReleaseUpdateRequest $request, Release $release): ReleaseResource
    {
        $release->update($request->validated());

        return new ReleaseResource($release);
    }

   public function destroy($id)
    {   
       
        Release::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
}

}