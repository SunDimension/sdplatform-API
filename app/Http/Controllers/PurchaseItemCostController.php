<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseItemCostStoreRequest;
use App\Http\Requests\PurchaseItemCostUpdateRequest;
use App\Http\Resources\PurchaseItemCostCollection;
use App\Http\Resources\PurchaseItemCostResource;
use App\Models\PurchaseItemCost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchaseItemCostController extends Controller
{
    public function index(Request $request): PurchaseItemCostCollection
    {
        $branches = PurchaseItemCost::all();

        return new PurchaseItemCostCollection($branches);
    }

    public function store(PurchaseItemCostStoreRequest $request): PurchaseItemCostResource
    {
        $branch = PurchaseItemCost::create($request->validated());

        return new PurchaseItemCostResource($branch);
    }

    public function show(Request $request, PurchaseItemCost $branch): PurchaseItemCostResource
    {
        return new PurchaseItemCostResource($branch);
    }

    public function update(PurchaseItemCostUpdateRequest $request, PurchaseItemCost $branch): PurchaseItemCostResource
    {
        $branch->update($request->validated());

        return new PurchaseItemCostResource($branch);
    }

    public function destroy($id)
    {
        try {
            $purchaseItemCost = PurchaseItemCost::findOrFail($id);
            $purchaseItemCost->delete();

            return response()->json([
                'message' => 'Purchase Item Cost deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Purchase Item Cost',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
