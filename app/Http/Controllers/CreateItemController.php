<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStoreRequest;
use App\Http\Requests\CreateItemUpdateRequest;
use App\Http\Resources\CreateItemCollection;
use App\Http\Resources\CreateItemResource;
use App\Models\CreateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreateItemController extends Controller
{
    public function index(Request $request)
    {   
   return CreateItemResource::collection(CreateItem::all());
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

    public function destroy($id)
    {
        CreateItem::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function search(Request $request): CreateItemCollection
    {
        $sname = $request->input('name', '');

        if (!empty($sname)) {
            $createItems = CreateItem::where('name', 'like', "%$sname%")
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $createItems = CreateItem::where('user_id', Auth::id())->get();
        }

        return new CreateItemCollection($createItems);
    }

    /**
     * Reduce stock quantity of the item.
     */
    public function reduceStock(Request $request, CreateItem $createItem, $id)
    {
        // Validate request
        $validated = $request->validate([
            'quantity_sold' => 'required|integer|min:1',
        ]);

           $createItem = CreateItem::findOrFail($id);

        // Ensure there's enough quantity available
        if ($createItem->quantity < $validated['quantity_sold']) {
            return response()->json([
                'message' => 'Not enough stock available'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Deduct the sold quantity from available stock
        $createItem->quantity -= $validated['quantity_sold'];
        $createItem->save();

        return new CreateItemResource($createItem);
    }
}
