<?php

namespace App\Http\Controllers;

use App\Classes\StockUtil;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseStoreRequest;
use App\Http\Requests\ReleaseUpdateRequest;
use App\Http\Resources\ReleaseCollection;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use App\Models\CreateItem;
use App\Models\Measurement;
use App\Models\ReleaseDetails;
use App\Models\ProductAudit;
use App\Models\SalesReceipt;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReleaseController extends Controller
{


    public function index(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'nullable|integer|exists:stores,id',
            'branch_id' => 'nullable|integer|exists:stores,branch_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'product_id' => 'nullable|integer|exists:create_items,id', // Add product_id filter
        ]);

        $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $productId = $validated['product_id'] ?? null;

        // Start building the query with eager loading
        $query = Release::with([
            'customer',
            'store',
            'user',
            'branch',
            'releasedetail',
            'releasedetail.product' // Load product details for each release detail
        ])
            ->when($storeId, function ($query, $storeId) {
                return $query->where('store_id', $storeId);
            })
            ->when($branchId, function ($query, $branchId) {
                return $query->whereHas('store', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            })
            ->when($productId, function ($query, $productId) {
                // Filter releases that have details with the specified product_id
                return $query->whereHas('releasedetail', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                });
            });

        // Handle date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        } elseif ($toDate) {
            $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
        }

        // Order by recent first
        $query->orderBy('created_at', 'desc');

        // Execute the query and get the filtered results
        $releases = $query->get();

        // Return as a resource collection
        return new ReleaseCollection($releases);
    }

    public function store(Request $request)
    {
        Log::debug($request->all());
        $errors = [];

        foreach ($request->items as $item) {
            $createItem = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $request->store_id)->first();
            Log::debug($createItem);
            $quantity = StockUtil::getActualQuantity($item['product_id'], $request->store_id);

            $unit = Measurement::where('id', $item['unit_measurement'])
                ->first()->name;

            if ($createItem && $quantity < StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity'])) {
                $createItem->load('createItem');
                $errors[] = $createItem->createItem->name;
            } elseif (!$createItem) {
                $errors[] = "Unknown item (ID: {$item['product_id']}) - Not found in store.";
            }
        }

        if (count($errors) > 0) {
            return response()->json(['error' => 'Insufficient stock for ' . implode(",", $errors)], Response::HTTP_BAD_REQUEST);
        }

        $salesReceipt = SalesReceipt::findOrFail($request->sales_receipt_id);
        if ($salesReceipt->blocked) {
            return response()->json(['message' => 'This receipt is blocked from release.'], 403);
        }

        $release = Release::create([
            'sales_receipt_id'   => $request->sales_receipt_id,
            'branch_id'          => $request->branch_id,
            'store_id'           => $request->store_id,
            'customer_id'        => $request->customer_id,
            'release_date'       => now(),
            'user_id'            => $request->user_id,
        ]);

        $items = [];
        foreach ($request->items as $item) {
            $createItem = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $request->store_id)->first();
            $unit = Measurement::where('id', $item['unit_measurement'])->first()->name;

            // Calculate quantity change in pieces
            $quantityChange = StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity']);
            $previousQuantity = StockUtil::getQuantityForRequest($item['product_id'], $item['store_id']);
            $newQuantity = StockUtil::getQuantityForRequest($item['product_id'], $item['store_id']);

            // Log the release in ProductAudit BEFORE creating ReleaseDetails
            ProductAudit::create([
                'action_type' => 'released',
                'product_id' => $item['product_id'],
                'user_id' => auth()->id(),
                'quantity_change' => -$quantityChange,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reference_type' => 'Release',
                'reference_id' => $release->id,
                'store_id' => $release->store_id,
                'notes' => 'Stock released from store'
            ]);

            ReleaseDetails::create([
                'release_id' => $release->id,
                'product_id' => $item['product_id'],
                'release_quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'unit_measurement' => $item['unit_measurement'],
                'quantity_pieces' => $quantityChange
            ]);

            $items[] = $item['product_id'];
        }

        $order = SalesReceipt::where('id', $request->sales_receipt_id)->first();
        $sql = "update item_solds set status ='released' where sales_order_id=" . $order->sales_order_id . " and store_id = " . $request->store_id . " and product_id in (" . implode(",", $items) . ")";
        DB::update($sql);

        return response()->json(['message' => 'Release Created Successfully', 'data' => $release], 200);
    }

    public function show(Request $request, Release $release): ReleaseResource
    {
        //Log::debug($release->load('releasedetail'));
        return new ReleaseResource($release->load('releasedetail'));
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
