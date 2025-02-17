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
use App\Models\ReleaseDetails;
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
        'branch_id' => 'nullable|integer|exists:stores,branch_id', // Ensure branch_id exists in stores
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
    ]);

    $storeId = $validated['store_id'] ?? null;
    $branchId = $validated['branch_id'] ?? null;
    $fromDate = $validated['from_date'] ?? null;
    $toDate = $validated['to_date'] ?? null;

    // Start building the query
    $query = Release::with(['customer', 'store', 'user', 'branch', 'releasedetail'])
        ->when($storeId, function ($query, $storeId) {
            return $query->where('store_id', $storeId);
        })
        ->when($branchId, function ($query, $branchId) {
            // Filter SalesOrder by matching branch_id in related Store
            return $query->whereHas('store', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        });

    // Handle date filtering with proper range logic
    if ($fromDate && $toDate) {
        // Ensure fromDate is not after toDate
        $query->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    } elseif ($fromDate) {
        $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
    } elseif ($toDate) {
        $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
    }

    // Execute the query and get the filtered results
    $releases = $query->get();

    // Return as a resource collection
    return new ReleaseCollection($releases);
    }


    public function store(ReleaseStoreRequest $request)
    {
        // Validate input (make sure you get both the create_item_id and quantity to be released)
        $validated = $request->validated();
        Log::debug($validated);
        $errors = [];
        foreach ($validated['items'] as $item) {
            $createItem = StoreItem::where('create_item_id',$item['product_id'])->where('store_id', $validated['store_id'])->first();
            // Check if there is enough stock
            Log::debug($createItem);
            $quantity = StockUtil::getActualQuantity($validated['product_id'], $validated['store_id']);
            if($quantity < $item['quantity']) {
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
            'user_id'            =>  $validated['user_id'],//Auth::id()
        ]); // The user who authorized the release

        $items = [];
        foreach ($validated['items'] as $item) {
            ReleaseDetails::create([
                'release_id' => $release->id,
                'product_id' => $item['product_id'],
                'release_quantity' => $item['quantity'],
                'amount' => $item['amount'],
            ]);
            $items[] = $item['product_id'];
            // Reduce stock from CreateItem (inventory)
            $createItem = StoreItem::where('create_item_id',$item['product_id'])->where('store_id', $validated['store_id'])->first();
            $createItem->quantity -= $item['quantity'];
            $createItem->quantity_holding -= $item['quantity'];
            $createItem->save();
        }

        $order= SalesReceipt::where('id', $validated['sales_receipt_id'])->first();

        $sql = "update item_solds set status ='released' where sales_order_id=".$order->sales_order_id. " and store_id = ".$validated['store_id']. " and product_id in (". implode(",", $items).")";
        DB::update($sql);
        //return new ReleaseResource($release);
        
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
