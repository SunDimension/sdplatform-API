<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceChangeStoreRequest;
use App\Http\Requests\PriceChangeUpdateRequest;
use App\Http\Resources\PriceChangeCollection;
use App\Http\Resources\PriceChangeResource;
use App\Models\PriceChange;
use Illuminate\Http\Request;
use App\Models\ProductAudit;
use App\Models\StoreItem;
use Illuminate\Support\Facades\DB;

class PriceChangeController extends Controller
{
    public function index(Request $request)
    {
        $priceChanges = PriceChange::all();
        return new PriceChangeCollection($priceChanges);
    }

    public function pending(Request $request)
    {
        $priceChanges = PriceChange::where('branch_id', auth()->user()->branch_id)->get();
        return new PriceChangeCollection($priceChanges);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required']
        ]);

        $priceChange = PriceChange::findOrFail($validated['id']);
        
        $priceChange->update([
            'comment' => $validated['comment'],
            'status' => $validated['status'],
            'approved_by' => auth()->id(),
            'approval_date' => now(),
        ]);

        if ($priceChange->status === 'Approved') {
            DB::beginTransaction();

            try {
                $details = $priceChange->details; // Already cast to array
                
                foreach ($details as $item) {
                    $storeItem = StoreItem::where('create_item_id', $item['product_id'])
                        ->where('store_id', $priceChange->store_id)
                        ->first();

                    if ($storeItem) {
                        $oldPrice = $storeItem->selling_price;
                        $newPrice = $item['new_selling_price'];

                        // Update the selling price
                        $storeItem->update(['selling_price' => $newPrice]);

                        // Create audit record only if price actually changed
                        if ($oldPrice != $newPrice) {
                            ProductAudit::create([
                                'action_type' => 'price_adjustment',
                                'product_id' => $item['product_id'],
                                'user_id' => auth()->id(),
                                'store_id' => $priceChange->store_id,
                                'previous_price' => $oldPrice,
                                'new_price' => $newPrice,
                                'price_change' => $newPrice - $oldPrice,
                                'reference_type' => 'PriceChange',
                                'reference_id' => $priceChange->id,
                                'notes' => 'Price adjustment approved'
                            ]);
                        }
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Selling prices updated successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return new PriceChangeResource($priceChange);
    }

    public function store(PriceChangeStoreRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $priceChange = PriceChange::create($data);

        return new PriceChangeResource($priceChange);
    }

    public function show(Request $request, PriceChange $priceChange)
    {
        return new PriceChangeResource($priceChange);
    }

    public function update(PriceChangeUpdateRequest $request, PriceChange $priceChange)
    {
        $priceChange->update($request->validated());
        return new PriceChangeResource($priceChange);
    }

    public function destroy(Request $request, PriceChange $priceChange)
    {
        $priceChange->delete();
        return response()->noContent();
    }
}