<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseStoreRequest;
use App\Http\Requests\ReleaseUpdateRequest;
use App\Http\Resources\ReleaseCollection;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use App\Models\CreateItem;
use App\Models\ReleaseDetails;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReleaseController extends Controller
{

    public function index(Request $request): ReleaseCollection
    {
        $release = Release::all();

        return new ReleaseCollection($release);
    }
    public function store(ReleaseStoreRequest $request)
    {
        // Validate input (make sure you get both the create_item_id and quantity to be released)
        $validated = $request->validated();
        
        $errors = [];
        foreach ($validated['items'] as $item) {
            $createItem = StoreItem::where('create_item_id',$item['product_id'])->where('store_id', $validated['store_id'])->first();
            // Check if there is enough stock
            Log::debug($createItem);
            if($createItem->quantity < $item['quantity']) {
                $createItem->load('createItem');
                $errors[] = $createItem->createItem->name;
                // return response()->json(['error' => 'Insufficient stock'], Response::HTTP_BAD_REQUEST);
            }
        }
        // Check if there is enough stock
        if (count($errors)>0) {
            return response()->json(['error' => 'Insufficient stock for '. implode(",", $errors)], Response::HTTP_BAD_REQUEST);
        }

    
        // Record the release in the Release table
        $release = Release::create([
            'sales_receipt_id'   => $validated['sales_receipt_id'],
            'branch_id'          => $validated['branch_id'],
            'store_id'           => $validated['store_id'],
            'customer_id'        => $validated['customer_id'],
            // 'create_item_id'     => $createItem->id, // From inventory
            // 'quantity_released'  => $validated['quantity_released'],
            'release_date'       => now(),
            'user_id'            => Auth::id()
        ]); // The user who authorized the release


        foreach ($validated['items'] as $item) {
            ReleaseDetails::create([
                'release_id' => $release->id,
                'product_id' => $item['product_id'],
                'release_quantity' => $item['quantity'],
                'amount' => $item['amount'],
            ]);

            // Reduce stock from CreateItem (inventory)
            $createItem = StoreItem::where('create_item_id',$item['product_id'])->where('store_id', $validated['store_id'])->first();
            $createItem->quantity -= $item['quantity'];
            $createItem->quantity_holding -= $item['quantity'];
            $createItem->save();
        }
        //return new ReleaseResource($release);
        
        return response()->json(['message' => 'Release Created Successfully', 'data' => $release], 200);
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
