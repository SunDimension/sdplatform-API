<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesOrderResource;
use App\Http\Resources\SalesOrderCollection;
use App\Http\Resources\StoreCollection;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\ItemSold;
use App\Models\PostInflow;
use App\Models\PostOutflow;
use App\Models\SalesInvoice;
use App\Models\SalesReceipt;
use App\Models\StoreItem;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Log as FacadesLog;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;

class SalesOrderController extends Controller
{

public function index(Request $request): SalesOrderCollection
{
    // Validate and retrieve query parameters from the request
    $validated = $request->validate([
        'store_id' => 'nullable|integer|exists:stores,id',
        'branch_id' => 'nullable|integer|exists:stores,branch_id',
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
    ]);

    // Extract validated parameters
    $storeId = $validated['store_id'] ?? null;
    $branchId = $validated['branch_id'] ?? null;
    $fromDate = $validated['from_date'] ?? null;
    $toDate = $validated['to_date'] ?? null;

    // Start building the query with all sales orders
    $query = SalesOrder::with(['customer', 'store', 'user', 'branch', 'itemsold']);

    // Apply store filter if provided
    if ($storeId) {
        $query->where('store_id', $storeId);
    }

    // Apply branch filter if provided
    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    // Apply date range filters if provided
    if ($fromDate || $toDate) {
        // Convert from_date and to_date to Carbon instances only if provided
        $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

        if ($fromDate && $toDate) {
            // Both from_date and to_date are provided
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            // Only from_date is provided
            $query->where('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            // Only to_date is provided
            $query->where('created_at', '<=', $toDate);
        }
    }

    // Fetch the results
    $salesOrders = $query->get();

    // Return the results as a collection
    return new SalesOrderCollection($salesOrders);
}





public function getStores(Request $request)
{
    $branchId = $request->query('branch_id'); // Retrieve branch_id from query parameter

    // Only fetch stores with the matching branch_id if provided
    $stores = Store::when($branchId, function ($query) use ($branchId) {
        $query->where('branch_id', $branchId);
    })->get();

    return response()->json($stores);
}



    // public function index(Request $request): SalesOrderCollection
    // {
    //     // Get query parameters from the request and validate
    //     $validated = $request->validate([
    //         'store_id' => 'nullable|integer|exists:stores,id',
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date',
    //     ]);

    //     // Log the validated input (if necessary for debugging)
    //     // FacadesLog::debug($validated);

    //     $storeId = $validated['store_id'] ?? null;
    //     $fromDate = $validated['from_date'] ?? null;
    //     $toDate = $validated['to_date'] ?? null;

    //     // Start building the query
    //     $query = SalesOrder::with(['customer', 'store', 'user', 'branch', 'itemsold']);

    //     // Apply filters based on the validated parameters
    //     if ($storeId) {
    //         $query->where('store_id', $storeId);
    //     }

    //     // Handle date filtering with proper range logic
    //     if ($fromDate && $toDate) {
    //         // Ensure fromDate is not after toDate
    //         $query->whereBetween('created_at', [
    //             Carbon::parse($fromDate)->startOfDay(),
    //             Carbon::parse($toDate)->endOfDay()
    //         ]);
    //     } elseif ($fromDate) {
    //         // Filter for records starting from fromDate
    //         $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
    //     } elseif ($toDate) {
    //         // Filter for records up to toDate
    //         $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
    //     }

    //     // Execute the query and get the filtered results
    //     $salesOrders = $query->get();

    //     // Return as a resource collection
    //     return new SalesOrderCollection($salesOrders);
    // }

    public function pendingReceipts(Request $request)
    {
        // Optionally, you can add filtering and sorting capabilities here
        $salesOrders = SalesOrder::where('status', 'Pending')->where("branch_id", auth()->user()->branch_id)->get();
        // Log::debug($salesOrders);
        return response()->json(['data' => SalesOrderResource::collection($salesOrders)]);
    }

    public function pendingCredit(Request $request)
    {
        // Optionally, you can add filtering and sorting capabilities here
        $salesOrders = SalesOrder::where('status', 'Credit Pending')->where('payment_type', 'Credit')->where("branch_id", auth()->user()->branch_id)->get();
        
        return response()->json(['data' => SalesOrderResource::collection($salesOrders)]);
    }

    public function getbynumber($orderno)
    {
        $salesOrders = SalesOrder::with('itemSold','customer')->where('sales_order_number', $orderno)->withSum('salesReceipts as total_paid', 'amount_paid')->first();

        $customerId = $salesOrders->customer_id;
        $inflows = PostInflow::where('customer_id', $customerId)->where('inflow_status',3)->sum('amount');;
        $outflows = PostOutflow::where('customer_id', $customerId)->sum('amount');

        Log::debug($inflows);
        Log::debug($outflows);

        $balance = $inflows-$outflows;

        return response()->json(['data' => new SalesOrderResource($salesOrders, $balance) ]);
    }

    public function show($id)
    {
        // Retrieve the sales order by its ID along with related data
        $salesOrder = SalesOrder::with('customer', 'branch', 'store', 'itemSold')->findOrFail($id);

        // Return the sales order as a JSON response
        return response()->json(['data' => new SalesOrderResource($salesOrder)]);
    }


    // Method to create a new Sales Order
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'credit_limit' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.quantity' => 'required|integer',
            'items.*.unit_price' => 'required|numeric',
            'items.*.store_id' => 'required|integer',
            'items.*.discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'invoice' => 'nullable|array',
            'payment' => 'nullable|array',
            'payment.total_amount' => 'required|numeric',
            'payment.amount_paid' => 'required|numeric',
            'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit', // Payment type now explicitly validated
            // 'payment.item_sold_id' => 'required|integer|exists:item_solds,id',  // item_sold_id is foreign key in sales_receipt
        ]);

        $errors = [];
        foreach ($validated['items'] as $item) {
            $createItem = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $item['store_id'])->first();
            // Check if there is enough 
            // Log::debug($createItem);
            if (($createItem->quantity - $createItem->quantity_holding) < $item['quantity']) {
                $createItem->load('createItem');
                $errors[] = $createItem->createItem->name;
                // return response()->json(['error' => 'Insufficient stock'], Response::HTTP_BAD_REQUEST);
            }
        }
        // Check if there is enough stock
        if (count($errors) > 0) {
            return response()->json(['error' => 'Insufficient stock for ' . implode(",", $errors)], HttpResponse::HTTP_BAD_REQUEST);
        }

        $salesOrderNumber = 'HGV-SO-' . strtoupper(uniqid());
        // Create a new Sales Order

        $order = [
            'sales_order_number' => $salesOrderNumber,
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'store_id' => $validated['store_id'],
            'user_id' => $validated['user_id'],
            // 'credit_limit' => $validated['credit_limit'] ?? null,
            'total_amount' => $validated['total_amount'] ?? null,
            'payment_type' => $validated['payment']['payment_type'],
        ];

        if($validated['payment']['payment_type']=='Credit')
        {
            $order["status"]= 'Credit Pending';
        }
        $salesOrder = SalesOrder::create($order);

        //Log::alert($validated);
        // Update Items Sold
        $itemSoldIds = [];
        foreach ($validated['items'] as $item) {
            $itemSold = ItemSold::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * ($item['unit_price'] - $item['discount']),
                'store_id' => $item['store_id'],
                'discount' => $item['discount'],
                'sales_date' => now(),

            ]);
            $itemSoldIds[] = $itemSold->id;

            $createItem2 = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $item['store_id'])->first();
            // $createItem->quantity -= $validated['quantity_released'];
            $createItem2->quantity_holding += $item['quantity'];
            $createItem2->save();
        }

        return response()->json(['message' => 'Sales Order Created Successfully', 'data' => $salesOrder], 200);
    }
    //   public function show(Request $request, SalesOrder $salesOrder): SalesOrderResource
    // {
    //     return new SalesOrderResource($salesOrder);
    // }
    // Method to fetch the Sales Order for editing
    public function edit($id)
    {
        $salesOrder = SalesOrder::with('itemSold', 'salesInvoices', 'salesReceipts')->findOrFail($id);

        return response()->json($salesOrder);
    }

    // Method to update an existing Sales Order
    public function update(Request $request, $id)
    {
        // Validate incoming request data excluding the auto-generated fields
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'store_id' => 'exists:stores,id',
            'credit_limit' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.quantity' => 'required|integer',
            'items.*.unit_price' => 'required|numeric',
            // 'invoice' => 'nullable|array',
            'total_amount' => 'required|numeric',
            // 'invoice' => 'nullable|array',
            'payment' => 'nullable|array',
            // 'payment.total_amount' => 'required|numeric',
            // 'payment.amount_paid' => 'required|numeric',
            'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit', 
        ]);

        // Find and update the Sales Order
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->update([
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'store_id' => $validated['store_id'],
            'credit_limit' => $validated['credit_limit'] ?? null,
            'total_amount' => $validated['total_amount'] ?? null,
            'payment_type' => $validated['payment']['payment_type'],

        ]);

        // Update Items Sold
        foreach ($validated['items'] as $item) {
            ItemSold::updateOrCreate(
                [
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $item['product_id'],
                ],
                [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                    'sales_date' => now(),
                ]
            );
        }

        // Update Sales Invoice if payment is deferred
        // if (!empty($validated['invoice'])) {
        //     SalesInvoice::updateOrCreate(
        //         [
        //             'sales_order_id' => $salesOrder->id,
        //             'sales_invoice_number' => $validated['invoice']['sales_invoice_number'], // Ensuring uniqueness
        //         ],
        //         [
        //             'product_id' => $validated['invoice']['product_id'],
        //             'unit_price' => $validated['invoice']['unit_price'],
        //             'amount' => $validated['invoice']['amount'],
        //             'invoice_date' => now(),
        //         ]
        //     );
        // }

        // Update Sales Receipt if payment is made
        // if (!empty($validated['payment'])) {
        //     SalesReceipt::updateOrCreate(
        //         [
        //             'sales_order_id' => $salesOrder->id,
        //             'sales_receipt_number' => $validated['payment']['sales_receipt_number'], // Ensuring uniqueness
        //         ],
        //         [
        //             'sales_invoice_id' => $validated['payment']['sales_invoice_id'],
        //             'customer_id' => $validated['customer_id'],
        //             'branch_id' => $validated['branch_id'],
        //             'store_id' => $validated['store_id'],
        //             'total_amount' => $validated['payment']['total_amount'],
        //             'amount_paid' => $validated['payment']['amount_paid'],
        //             'payment_mode_id' => $validated['payment']['payment_mode_id'],
        //             'receipt_date' => now(),
        //         ]
        //     );
        // }

        return response()->json(['message' => 'Sales Order Updated Successfully', 'sales_order' => $salesOrder], 200);
    }
}