<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesOrderResource;
use App\Http\Resources\SalesOrderCollection;
use App\Http\Resources\ItemSoldResource;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\ItemSoldCollection;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\ItemSold;
use App\Models\PostInflow;
use App\Models\ProductAudit;
use App\Models\PostOutflow;
use App\Models\CreditTransaction;
use App\Models\Customer;
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
use App\Classes\ProcessDelination;
use App\Classes\StockUtil;
use App\Http\Resources\ReturnItemResource;
use App\Models\Measurement;
use App\Models\ReturnDetails;
use App\Models\ReturnItem;

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

        // Apply branch filter if provided or based on the logged-in user
        if ($branchId) {
            $query->where('branch_id', $branchId);
        } else {
        }

        // Apply date range filters if provided
        if ($fromDate || $toDate) {
            // Convert from_date and to_date to Carbon instances only if provided
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            // Apply date filter for the selected range along with the branch condition
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

            // Ensure transactions are fetched only for the user's branch when filtering by date
            $user = auth()->user(); // Get the logged-in user
            $query->where('branch_id', $user->branch_id); // Filter by branch_id (user's branch)
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
        $salesOrders = SalesOrder::with('itemSold', 'customer', 'creditTransaction')->where('sales_order_number', $orderno)->withSum('salesReceipts as total_paid', 'amount_paid')->first();

        $customerId = $salesOrders->customer_id;
        $inflows = PostInflow::where('customer_id', $customerId)->where('inflow_status', 3)->sum('amount');;
        $outflows = PostOutflow::where('customer_id', $customerId)->sum('amount');

        Log::debug($inflows);
        Log::debug($outflows);

        $balance = $inflows - $outflows;

        return response()->json(['data' => new SalesOrderResource($salesOrders, $balance)]);
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
            'items.*.unit_measurement' => 'required|integer',
            'items.*.unit_price' => 'required|numeric',
            'items.*.store_id' => 'required|integer',
            'items.*.discount' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
            'invoice' => 'nullable|array',
            'payment' => 'nullable|array',
            'payment.total_amount' => 'required|numeric',
            'payment.amount_paid' => 'required|numeric',
            'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit',
        ]);

        $errors = [];

        foreach ($validated['items'] as $item) {
            $storeItem = StoreItem::where('create_item_id', $item['product_id'])
                ->where('store_id', $item['store_id'])
                ->first();

            $unit = Measurement::where('id', $item['unit_measurement'])
                ->first()->name;

            if (!$storeItem) {
                $errors[] = "Item not found in store.";
                continue;
            }

            $quantityAvailable = StockUtil::getQuantityForRequest($item['product_id'], $item['store_id']);
            $item['quantity_pieces'] = StockUtil::getPieceQuivalent($unit, $storeItem['quantity_in_package'], $item['quantity']);
            // Check if requested quantity exceeds available stock
            if ($quantityAvailable < $item['quantity_pieces']) {
                $storeItem->load('createItem');
                $errors[] = "Insufficient stock for " . $storeItem->createItem->name;
            }

            //Enforce set_limit restriction
            if ($storeItem->set_limit !== null && $item['quantity'] > $storeItem->set_limit) {
                $storeItem->load('createItem');
                $errors[] = "Sale quantity for " . $storeItem->createItem->name .
                    " exceeds the allowed limit of " . $storeItem->set_limit . " per transaction.";
            }
        }

        // If any errors were found, return a bad request response
        if (count($errors) > 0) {
            return response()->json(['error' => implode(", ", $errors)], 400);
        }

        // Generate Sales Order Number
        $salesOrderNumber = 'HGV-SO-' . strtoupper(uniqid());

        // Create a new Sales Order
        $order = [
            'sales_order_number' => $salesOrderNumber,
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'store_id' => $validated['store_id'],
            'user_id' => $validated['user_id'],
            'total_amount' => $validated['total_amount'],
            'payment_type' => $validated['payment']['payment_type'],
        ];

        if ($validated['payment']['payment_type'] == 'Credit') {
            $order["status"] = 'Credit Pending';
        }

        $salesOrder = SalesOrder::create($order);

        // Process Each Item in the Order
        $itemSoldIds = [];

        foreach ($validated['items'] as $item) {
            $unit = Measurement::where('id', $item['unit_measurement'])->first()->name;
            $createItem = StoreItem::where('create_item_id', $item['product_id'])->where('store_id', $request->store_id)->first();
            $itemSold = ItemSold::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * ($item['unit_price'] - $item['discount']),
                'store_id' => $item['store_id'],
                'discount' => $item['discount'],
                'quantity_pieces' => StockUtil::getPieceQuivalent($unit, $createItem['quantity_in_package'], $item['quantity']),
                'unit_measurement' => $item['unit_measurement'],
                'sales_date' => now(),
            ]);

            $itemSoldIds[] = $itemSold->id;

            // Update Stock: Increase quantity_holding
            $storeItem = StoreItem::where('create_item_id', $item['product_id'])
                ->where('store_id', $item['store_id'])
                ->first();

            $storeItem->quantity_holding += $item['quantity'];
            $storeItem->save();
        }

        return response()->json([
            'message' => 'Sales Order Created Successfully',
            'data' => $salesOrder
        ], 200);
    }

    //   public function show(Request $request, SalesOrder $salesOrder): SalesOrderResource
    // {
    //     return new SalesOrderResource($salesOrder);
    // }
    // Method to fetch the Sales Order for editing
    // public function edit($id)
    // {
    //     $salesOrder = SalesOrder::with('itemSold', 'salesInvoices', 'salesReceipts')->findOrFail($id);

    //     return response()->json($salesOrder);
    // }

    // // Method to update an existing Sales Order
    // public function update(Request $request, $id)
    // {
    //     // Validate incoming request data excluding the auto-generated fields
    //     $validated = $request->validate([
    //         'customer_id' => 'required|exists:customers,id',
    //         'branch_id' => 'required|exists:branches,id',
    //         'store_id' => 'exists:stores,id',
    //         'credit_limit' => 'nullable|numeric',
    //         'items' => 'required|array',
    //         'items.*.product_id' => 'required|exists:create_items,id',
    //         'items.*.quantity' => 'required|integer',
    //         'items.*.unit_price' => 'required|numeric',
    //         // 'invoice' => 'nullable|array',
    //         'total_amount' => 'required|numeric',
    //         // 'invoice' => 'nullable|array',
    //         'payment' => 'nullable|array',
    //         // 'payment.total_amount' => 'required|numeric',
    //         // 'payment.amount_paid' => 'required|numeric',
    //         'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit',
    //     ]);

    //     // Find and update the Sales Order
    //     $salesOrder = SalesOrder::findOrFail($id);
    //     $salesOrder->update([
    //         'customer_id' => $validated['customer_id'],
    //         'branch_id' => $validated['branch_id'],
    //         'store_id' => $validated['store_id'],
    //         'credit_limit' => $validated['credit_limit'] ?? null,
    //         'total_amount' => $validated['total_amount'] ?? null,
    //         'payment_type' => $validated['payment']['payment_type'],

    //     ]);

    //     // Update Items Sold
    //     foreach ($validated['items'] as $item) {
    //         ItemSold::updateOrCreate(
    //             [
    //                 'sales_order_id' => $salesOrder->id,
    //                 'product_id' => $item['product_id'],
    //             ],
    //             [
    //                 'quantity' => $item['quantity'],
    //                 'unit_price' => $item['unit_price'],
    //                 'amount' => $item['quantity'] * $item['unit_price'],
    //                 'sales_date' => now(),
    //             ]
    //         );
    //     }





    //     return response()->json(['message' => 'Sales Order Updated Successfully', 'sales_order' => $salesOrder], 200);
    // }

    public function edit($id)
    {
        // Retrieve the sales order with all related data needed for editing
        $salesOrder = SalesOrder::with([
            'customer',
            'branch',
            'store',
            'itemSold' => function ($query) {
                $query->with(['product', 'store', 'measurement']);
            }
        ])->findOrFail($id);

        return response()->json([
            'data' => new SalesOrderResource($salesOrder)
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array',
            'items.*.id' => 'nullable|exists:item_solds,id', // For existing items
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.unit_measurement' => 'required|exists:measurements,id',
            'payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit',
            'total_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Find the sales order
            $salesOrder = SalesOrder::findOrFail($id);

            // Update sales order details
            $salesOrder->update([
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'store_id' => $validated['store_id'],
                'total_amount' => $validated['total_amount'],
                'payment_type' => $validated['payment_type'],
            ]);

            // Process items
            $currentItemIds = [];

            foreach ($validated['items'] as $itemData) {
                $itemData['amount'] = ($itemData['unit_price'] - ($itemData['discount'] ?? 0)) * $itemData['quantity'];

                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = ItemSold::find($itemData['id']);
                    $item->update($itemData);
                    $currentItemIds[] = $item->id;
                } else {
                    // Add new item
                    $newItem = ItemSold::create(array_merge($itemData, [
                        'sales_order_id' => $salesOrder->id,
                        'sales_date' => now(),
                    ]));
                    $currentItemIds[] = $newItem->id;
                }
            }

            // Delete items not in the current request
            ItemSold::where('sales_order_id', $salesOrder->id)
                ->whereNotIn('id', $currentItemIds)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'Sales Order Updated Successfully',
                'data' => new SalesOrderResource($salesOrder)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating sales order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function cancel($id)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the sales order
            $salesOrder = SalesOrder::with('itemSold')->findOrFail($id);

            // Check if the sales order is already canceled
            if ($salesOrder->status === 'Canceled') {
                return response()->json(['message' => 'Sales Order is already canceled.'], 400);
            }

            // Loop through items in the sales order and restore stock
            foreach ($salesOrder->itemSold as $itemSold) {
                $storeItem = StoreItem::where('create_item_id', $itemSold->product_id)
                    ->where('store_id', $salesOrder->store_id)
                    ->first();

                // If StoreItem is found, restore stock
                if ($storeItem) {
                    $storeItem->quantity_holding -= $itemSold->quantity;
                    $storeItem->save();
                } else {
                    // If StoreItem is not found, log an error (optional) and proceed
                    Log::warning("StoreItem for product {$itemSold->product_id} not found in store {$salesOrder->store_id}");
                    // Optionally, you can also throw an exception here if critical
                }
            }


            $salesOrder->delete();
            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Sales Order Canceled Successfully.'], 200);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log the error for debugging
            Log::error('Error canceling sales order: ' . $e->getMessage());

            // Return a response with error message
            return response()->json(['message' => 'An error occurred while canceling the order.'], 500);
        }
    }




    public function searchReceipt(Request $request)
    {
        $validated = $request->validate([
            'sales_receipt_number' => 'required|string|exists:sales_receipts,sales_receipt_number'
        ]);

        try {
            $receipt = SalesReceipt::with([
                'salesOrder' => function ($query) {
                    $query->with(['customer', 'branch', 'store', 'itemSold' => function ($q) {
                        $q->with(['product', 'store']);
                    }]);
                }
            ])->where('sales_receipt_number', $validated['sales_receipt_number'])->firstOrFail();

            // Check if the sales receipt has a corresponding record in the releases table
            $releaseExists = DB::table('releases')
                ->where('sales_receipt_id', $receipt->id)
                ->exists();

            if (!$releaseExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot process return: No release record found for this receipt'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'receipt' => $receipt,
                    'order' => $receipt->salesOrder,
                    'customer' => $receipt->salesOrder->customer,
                    'items' => $receipt->salesOrder->itemSold->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? 'Unknown',
                            'quantity' => $item->quantity,
                            'quantity_pieces' => $item->quantity_pieces,
                            'unit_price' => $item->unit_price,
                            'discount' => $item->discount ?? 0,
                            'store_id' => $item->store_id,
                            'store_name' => $item->store->name ?? 'Unknown',
                            'unit_measurement' => $item->unit_measurement,
                            'item_sold_id' => $item->id // Important for returns
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Process a return request
     */
    public function processReturn(Request $request)
    {
        $validated = $request->validate([
            'sales_receipt_number' => 'required|exists:sales_receipts,sales_receipt_number',
            'return_status' => 'required|in:Approved,Pending,Declined',
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.item_sold_id' => 'required|exists:item_solds,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.quantity_returned_pieces' => 'required|integer|min:1',
            'items.*.original_quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.unit_measurement' => 'required|exists:measurements,id',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // 1. Find the sales receipt
            $salesReceipt = SalesReceipt::where('sales_receipt_number', $validated['sales_receipt_number'])
                ->firstOrFail();

            // 2. Verify receipt hasn't already been returned
            if ($salesReceipt->is_returned) {
                throw new \Exception("This receipt has already been processed for returns");
            }

            // 3. Get the associated sales order
            $salesOrder = SalesOrder::findOrFail($salesReceipt->sales_order_id);

            $return = new ReturnItem();
            $return->sales_receipt_id = $salesReceipt->id;
            $return->branch_id = $salesOrder->branch_id;
            $return->customer_id = $salesOrder->customer_id;
            $return->store_id = $salesOrder->store_id;
            $return->return_date = now();
            $return->created_by = auth()->user()->id;
            $return->return_status = $validated['return_status'];
            $return->notes = $validated['notes'] ?? null;
            $return->save();
            // 5. Process each returned item
            $processedItems = [];
            $totalReturnAmount = 0;

            foreach ($validated['items'] as $itemData) {
                // Find the original sold item
                $itemSold = ItemSold::findOrFail($itemData['item_sold_id']);

                // Validate item belongs to this order
                if ($itemSold->sales_order_id != $salesOrder->id) {
                    throw new \Exception("Item does not belong to this sales order");
                }

                // Validate return quantity
                if ($itemData['quantity_returned'] > $itemData['original_quantity']) {
                    throw new \Exception(
                        "Return quantity for product ID {$itemData['product_id']} exceeds original quantity"
                    );
                }

                // Calculate return amount
                $returnAmount = $itemData['quantity_returned'] * $itemData['unit_price'];
                $totalReturnAmount += $returnAmount;

                // Update item_sold record
                $returnDetails = new ReturnDetails();
                $returnDetails->return_id = $return->id;
                $returnDetails->product_id = $itemData['product_id'];
                $returnDetails->return_quantity = $itemData['quantity_returned'];
                $returnDetails->item_sold_id = $itemData['item_sold_id'];
                $returnDetails->unit_price = $itemData['unit_price'];
                $returnDetails->store_id = $itemData['store_id'];
                $returnDetails->return_quantity_pieces = $itemData['quantity_returned_pieces'];
                $returnDetails->unit_measurement = $itemData['unit_measurement'];
                $returnDetails->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return processed successfully',
                'data' => [
                    'receipt_number' => $salesReceipt->sales_receipt_number,
                    'order_id' => $salesOrder->id,
                    // 'return_type' => $salesOrder->return_type_id,
                    'return' => new ReturnItemResource($return),
                    'total_return_amount' => $totalReturnAmount,
                    'items_returned' => count($processedItems),
                    'processed_items' => $processedItems,
                ]
            ]);
            ProductAudit::create([
                'action_type' => 'returned',
                'product_id' => $itemData['product_id'],
                'user_id' => auth()->id(),
                'quantity_change' => $itemData['quantity_returned'],
                'reference_type' => 'ReturnItem',
                'reference_id' => $return->id,
                'notes' => 'Product returned'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Return processing failed',
                'error' => $e->getMessage()
            ], 400);
        }
        // In SalesOrderController (processReturn method)

    }




    /**
     * Restore stock to inventory after return
     */
    protected function restoreStock($productId, $storeId, $quantityReturned, $measurementId)
    {
        $storeItem = StoreItem::where('create_item_id', $productId)
            ->where('store_id', $storeId)
            ->firstOrFail();

        // Get measurement unit
        $measurement = Measurement::findOrFail($measurementId);
        $unit = $measurement->name;

        // Convert returned quantity to pieces
        $quantityInPieces = StockUtil::getPieceQuivalent(
            $unit,
            $storeItem->quantity_in_package,
            $quantityReturned
        );

        // Update inventory
        $storeItem->increment('quantity_available', $quantityInPieces);
        $storeItem->increment('quantity_request', $quantityInPieces);

        return $quantityInPieces;
    }

    public function processCreditReturn(Request $request)
    {
        $validated = $request->validate([
            'credit_order_number' => 'required|exists:credit_transactions,credit_order_number',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.item_sold_id' => 'required|exists:item_solds,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.quantity_returned_pieces' => 'required|integer|min:0',
            'items.*.original_quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.unit_measurement' => 'required|exists:measurements,id',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // 1. Find the credit transaction
            $creditTransaction = CreditTransaction::where('credit_order_number', $validated['credit_order_number'])
                ->where('type', 'credit')
                ->firstOrFail();

            // 2. Get the associated sales order
            $salesOrder = SalesOrder::findOrFail($creditTransaction->sales_order_id);

            // 3. Verify this is a credit purchase
            if ($salesOrder->payment_type !== 'Credit') {
                throw new \Exception("This order is not a credit purchase");
            }

            // 4. Create return record
            $return = new ReturnItem();
            $return->credit_transaction_id = $creditTransaction->id;
            $return->branch_id = $salesOrder->branch_id;
            $return->customer_id = $salesOrder->customer_id;
            $return->store_id = $salesOrder->store_id;
            $return->return_date = now();
            $return->created_by = auth()->id();
            $return->return_status = 'Approved'; // Auto-approve credit returns
            $return->notes = $validated['notes'] ?? null;
            $return->save();

            // 5. Process each returned item and calculate total return amount
            $totalReturnAmount = 0;
            $processedItems = [];

            foreach ($validated['items'] as $itemData) {
                // Validate item belongs to this order
                $itemSold = ItemSold::where('id', $itemData['item_sold_id'])
                    ->where('sales_order_id', $salesOrder->id)
                    ->firstOrFail();

                // Validate return quantity
                if ($itemData['quantity_returned'] > $itemData['original_quantity']) {
                    throw new \Exception(
                        "Return quantity for product ID {$itemData['product_id']} exceeds original quantity"
                    );
                }

                $returnAmount = $itemData['quantity_returned'] * $itemData['unit_price'];
                $totalReturnAmount += $returnAmount;

                // Create return detail
                $returnDetails = new ReturnDetails();
                $returnDetails->return_id = $return->id;
                $returnDetails->product_id = $itemData['product_id'];
                $returnDetails->return_quantity = $itemData['quantity_returned'];
                $returnDetails->item_sold_id = $itemData['item_sold_id'];
                $returnDetails->unit_price = $itemData['unit_price'];
                $returnDetails->store_id = $itemData['store_id'];
                $returnDetails->return_quantity_pieces = $itemData['quantity_returned_pieces'];
                $returnDetails->unit_measurement = $itemData['unit_measurement'];
                $returnDetails->save();

                $processedItems[] = $returnDetails;
            }

            // 6. Update customer's credit balance
            $customer = Customer::findOrFail($salesOrder->customer_id);
            $previousBalance = $customer->credit_balance;
            $customer->credit_balance += $totalReturnAmount; // Increase balance since we're returning goods
            $customer->save();

            // 7. Create credit adjustment transaction
            $adjustment = new CreditTransaction();
            $adjustment->branch_id = $salesOrder->branch_id;
            $adjustment->customer_id = $customer->id;
            $adjustment->sales_order_id = $salesOrder->id;
            $adjustment->amount = $totalReturnAmount;
            $adjustment->credit_balance_before = $previousBalance;
            $adjustment->credit_balance_after = $customer->credit_balance;
            $adjustment->type = 'return_adjustment';
            $adjustment->transaction_date = now();
            $adjustment->created_by = auth()->id();
            $adjustment->notes = "Credit adjustment for return #{$return->id}";
            $adjustment->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credit return processed successfully',
                'data' => [
                    'credit_order_number' => $creditTransaction->credit_order_number,
                    'return_id' => $return->id,
                    'customer_id' => $customer->id,
                    'previous_credit_balance' => $previousBalance,
                    'new_credit_balance' => $customer->credit_balance,
                    'amount_credited' => $totalReturnAmount,
                    'items_returned' => count($processedItems),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Credit return processing failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // Add this method to your SalesOrderController

    /**
     * Generate a product availability report
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productAvailabilityReport(Request $request)
    {
        // Validate request parameters     
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'nullable|exists:create_items,id',
        ]);

        // Extract validated parameters     
        $storeId = $validated['store_id'];
        $productId = $validated['product_id'] ?? null;
        $fromDate = $validated['from_date'] ? Carbon::parse($validated['from_date'])->startOfDay() : null;
        $toDate = $validated['to_date'] ? Carbon::parse($validated['to_date'])->endOfDay() : null;

        // If product_id is provided, return specific product availability     
        if ($productId) {
            $quantityAvailable = StockUtil::getQuantityForRequest($productId, $storeId);

            $product = StoreItem::with('createItem')
                ->where('create_item_id', $productId)
                ->where('store_id', $storeId)
                ->first();

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found in the selected store',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'data' => [
                    'item_id' => $product->create_item_id,
                    'item_name' => $product->createItem->name,
                    'available_quantity' => $quantityAvailable,
                ]
            ]);
        }

        // If no product_id, return all products in the store with their availability
        $storeItems = StoreItem::with('createItem')
            ->where('store_id', $storeId)
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {

                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->get();

        $reportData = $storeItems->map(function ($item) use ($storeId) {
            return [
                'item_id' => $item->create_item_id,
                'item_name' => $item->createItem->name,
                'available_quantity' => StockUtil::getQuantityForRequest($item->create_item_id, $storeId),
            ];
        });

        return response()->json([
            'data' => $reportData
        ]);
    }



    // public function salesReport(Request $request)
    // {
    //     $validated = $request->validate([
    //         'store_id' => 'nullable|integer|exists:stores,id',
    //         'product_id' => 'nullable|integer|exists:create_items,product_id',
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date',
    //         'page' => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //     ]);

    //     $storeId = $validated['store_id'] ?? null;
    //     $productId = $validated['product_id'] ?? null;
    //     $fromDate = $validated['from_date'] ?? null;
    //     $toDate = $validated['to_date'] ?? null;
    //     $perPage = $validated['per_page'] ?? 10;

    //     $query = ItemSold::with(['store', 'product', 'salesOrder']);

    //     if ($storeId) {
    //         $query->where('store_id', $storeId);
    //     }

    //     if ($productId) {
    //         $query->where('product_id', $productId);
    //     }

    //     if ($fromDate || $toDate) {
    //         $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    //         $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    //         if ($fromDate && $toDate) {
    //             $query->whereBetween('created_at', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $query->where('created_at', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $query->where('created_at', '<=', $toDate);
    //         }
    //     }

    //     $results = $query->paginate($perPage);

    //     // Calculate summary data
    //     $summary = [
    //         'total_records' => $results->total(),
    //         'total_quantity' => $results->sum('quantity'),
    //         'total_quantity_pieces' => $results->sum('quantity_pieces'), // adjust if your field is different
    //         'total_amount' => $results->sum('amount'),
    //         'total_discount' => $results->sum('discount'),
    //         'net_amount' => $results->sum('amount') - $results->sum('discount'),
    //         'date_range' => [
    //             'from' => $fromDate ? $fromDate->format('Y-m-d') : null,
    //             'to' => $toDate ? $toDate->format('Y-m-d') : null,
    //         ],
    //         'filters' => [
    //             'store_id' => $storeId,
    //             'product_id' => $productId,
    //         ]
    //     ];

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sales report generated successfully',
    //         'data' => ItemSoldResource::collection($results),
    //         'summary' => $summary
    //     ]);
    // }

    // public function salesReport(Request $request)
    // {
    //     // Log raw query parameters for debugging
    //     Log::info('Raw Request Parameters:', $request->query());

    //     $validated = $request->validate([
    //         'store_id' => 'nullable|integer|exists:stores,id',
    //         'storeId' => 'nullable|integer|exists:stores,id', // Add alternative naming
    //         'product_id' => 'nullable|integer|exists:create_items,product_id',
    //         'productId' => 'nullable|integer|exists:create_items,product_id', // Add alternative naming
    //         'from_date' => 'nullable|date',
    //         'fromDate' => 'nullable|date', // Add alternative naming
    //         'to_date' => 'nullable|date',
    //         'toDate' => 'nullable|date', // Add alternative naming
    //         'page' => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //     ]);

    //     // Use null coalescing to handle both naming conventions
    //     $storeId = $validated['store_id'] ?? $validated['storeId'] ?? null;
    //     $productId = $validated['product_id'] ?? $validated['productId'] ?? null;
    //     $fromDate = $validated['from_date'] ?? $validated['fromDate'] ?? null;
    //     $toDate = $validated['to_date'] ?? $validated['toDate'] ?? null;
    //     $perPage = $validated['per_page'] ?? 10;

    //     // Log validated parameters
    //     Log::info('Validated Parameters:', compact('storeId', 'productId', 'fromDate', 'toDate', 'perPage'));

    //     // Build the base query
    //     $query = ItemSold::with(['store', 'product', 'salesOrder']);

    //     // Apply store filter
    //     if ($storeId) {
    //         $query->where('store_id', $storeId);
    //     }

    //     // Apply product filter
    //     if ($productId) {
    //         $query->where('product_id', $productId);
    //     }

    //     // Apply date filters
    //     if ($fromDate || $toDate) {
    //         $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    //         $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    //         if ($fromDate && $toDate) {
    //             $query->whereBetween('sales_date', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $query->where('sales_date', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $query->where('sales_date', '<=', $toDate);
    //         }
    //     }

    //     // Log the query
    //     Log::info('Sales Report Query:', [
    //         'sql' => $query->toSql(),
    //         'bindings' => $query->getBindings(),
    //         'filters' => compact('storeId', 'productId', 'fromDate', 'toDate')
    //     ]);

    //     // Get paginated results
    //     $results = $query->paginate($perPage);

    //     // Calculate summary data from the FULL query (not paginated)
    //     $summaryQuery = ItemSold::query();

    //     // Apply the same filters to summary query
    //     if ($storeId) {
    //         $summaryQuery->where('store_id', $storeId);
    //     }

    //     if ($productId) {
    //         $summaryQuery->where('product_id', $productId);
    //     }

    //     if ($fromDate || $toDate) {
    //         if ($fromDate && $toDate) {
    //             $summaryQuery->whereBetween('sales_date', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $summaryQuery->where('sales_date', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $summaryQuery->where('sales_date', '<=', $toDate);
    //         }
    //     }

    //     // Calculate summary values
    //     $totalRecords = $summaryQuery->count();
    //     $totalQuantity = $summaryQuery->sum('quantity');
    //     $totalQuantityPieces = $summaryQuery->sum('quantity_pieces') ?? 0;
    //     $totalAmount = $summaryQuery->sum('amount');
    //     $totalDiscount = $summaryQuery->sum('discount');

    //     $summary = [
    //         'total_records' => $totalRecords,
    //         'total_quantity' => $totalQuantity,
    //         'total_quantity_pieces' => $totalQuantityPieces,
    //         'total_amount' => $totalAmount,
    //         'total_discount' => $totalDiscount,
    //         'net_amount' => $totalAmount - $totalDiscount,
    //         'date_range' => [
    //             'from' => $fromDate ? $fromDate->format('Y-m-d') : null,
    //             'to' => $toDate ? $toDate->format('Y-m-d') : null,
    //         ],
    //         'filters' => [
    //             'store_id' => $storeId,
    //             'product_id' => $productId,
    //         ]
    //     ];

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sales report generated successfully',
    //         'data' => ItemSoldResource::collection($results->items()),
    //         'summary' => $summary,
    //         'pagination' => [
    //             'current_page' => $results->currentPage(),
    //             'per_page' => $results->perPage(),
    //             'total' => $results->total(),
    //             'last_page' => $results->lastPage(),
    //             'from' => $results->firstItem(),
    //             'to' => $results->lastItem(),
    //         ]
    //     ]);
    // }
    // public function SalesSummary(Request $request)
    // {
    //     // Validate and retrieve query parameters from the request
    //     $validated = $request->validate([
    //         'store_id' => 'nullable|integer|exists:stores,id',
    //         'product_id' => 'nullable|integer|exists:create_items,product_id',
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date',
    //     ]);

    //     // Extract validated parameters
    //     $storeId = $validated['store_id'] ?? null;
    //     $producId = $validated['product_id'] ?? null;
    //     $fromDate = $validated['from_date'] ?? null;
    //     $toDate = $validated['to_date'] ?? null;

    //     $query = ItemSold::with(['salesOrder', 'store', 'product']);

    //     if ($storeId) {
    //         $query->where('store_id', $storeId);
    //     }

    //     if ($producId) {
    //         $query->where('product_id', $producId);
    //     } else {
    //     }

    //     // Apply date range filters if provided
    //     if ($fromDate || $toDate) {
    //         $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    //         $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    //         if ($fromDate && $toDate) {

    //             $query->whereBetween('sales_date', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $query->where('sales_date', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $query->where('sales_date', '<=', $toDate);
    //         }

    //         $user = auth()->user(); // Get the logged-in user
    //         $query->where('branch_id', $user->branch_id); // Filter by branch_id (user's branch)
    //     }

    //     // Fetch the results
    //     $item_sold = $query->get();


    //     return new ItemSoldResource($item_sold);
    // }

    public function salesSummary(Request $request)
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'store_id' => 'nullable|integer|exists:stores,id',
            'product_id' => 'nullable|integer|exists:create_items,product_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Extract validated parameters
        $storeId = $validated['store_id'] ?? null;
        $productId = $validated['product_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 10;

        // Get the logged-in user
        $user = auth()->user();

        // Build the query
        $query = ItemSold::with(['salesOrder', 'store', 'product']);

        // Filter by user's branch
        if ($user && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        // Filter by store if provided
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Filter by product if provided
        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Apply date range filters if provided
        if ($fromDate || $toDate) {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('sales_date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('sales_date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('sales_date', '<=', $toDate);
            }
        }

        // Get total count for pagination
        $totalCount = $query->count();

        // Apply pagination
        $itemsSold = $query->orderBy('sales_date', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Calculate summary data
        $summaryQuery = ItemSold::query();

        // Apply the same filters for summary
        if ($user && $user->branch_id) {
            $summaryQuery->where('branch_id', $user->branch_id);
        }
        if ($storeId) {
            $summaryQuery->where('store_id', $storeId);
        }
        if ($productId) {
            $summaryQuery->where('product_id', $productId);
        }
        if ($fromDate || $toDate) {
            if ($fromDate && $toDate) {
                $summaryQuery->whereBetween('sales_date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $summaryQuery->where('sales_date', '>=', $fromDate);
            } elseif ($toDate) {
                $summaryQuery->where('sales_date', '<=', $toDate);
            }
        }

        $summary = [
            'total_amount' => $summaryQuery->sum('amount'),
            'total_quantity' => $summaryQuery->sum('quantity'),
            'total_return_quantity' => $summaryQuery->sum('return_quantity'),
            'total_records' => $totalCount,
            'date_range' => [
                'from' => $fromDate ? $fromDate->format('Y-m-d') : null,
                'to' => $toDate ? $toDate->format('Y-m-d') : null,
            ]
        ];

        // Return structured response
        return response()->json([
            'success' => true,
            'data' => ItemSoldResource::collection($itemsSold),
            'summary' => $summary,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalCount,
                'total_pages' => ceil($totalCount / $perPage),
            ]
        ]);
    }
    public function SalesReport(Request $request)
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'product_id' => 'nullable|integer|exists:create_items,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Extract validated parameters
        $storeId = $validated['store_id'];
        $productId = $validated['product_id'] ?? null;
        $fromDate = Carbon::parse($validated['from_date'])->startOfDay();
        $toDate = Carbon::parse($validated['to_date'])->endOfDay();

        // Start building the query
        $query = ItemSold::with(['product'])
            ->where('store_id', $storeId)
            ->whereBetween('sales_date', [$fromDate, $toDate])
            ->whereNull('deleted_at');

        // Apply product filter if provided
        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Group by product and calculate total quantity
        $results = $query->select([
            'product_id',
            DB::raw('create_items.name as product_name'),
            DB::raw('SUM(quantity) as total_quantity')
        ])
            ->join('create_items', 'item_solds.product_id', '=', 'create_items.id')
            ->groupBy('product_id', 'create_items.name')
            ->get();

        return ItemSoldResource::collection($results);
    }

    public function mystoreItemSold(Request $request): ItemSoldCollection
    {
        //$store = Store::all();
        $store = Store::where('branch_id', auth()->user()->branch_id)->get();
        return new ItemSoldCollection($store->load('itemSolds'));
    }
}
