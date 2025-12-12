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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use App\Classes\ProcessDelination;
use App\Classes\StockUtil;
use App\Http\Resources\ReturnItemResource;
use App\Models\Measurement;
use App\Models\ReturnDetails;
use App\Models\ReturnItem;

use Illuminate\Support\Str;

class SalesOrderController extends Controller
{






    public function index(Request $request): SalesOrderCollection
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'store_id' => 'nullable|string|exists:stores,id',
            'branch_id' => 'nullable|string|exists:stores,branch_id',
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



    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_measurement' => 'required|exists:measurements,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.store_id' => 'required|string|exists:stores,id',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit',
            'payment.amount_paid' => 'required|numeric',
            'payment.total_amount' => 'required|numeric',
        ]);

        $errors = [];
        $auditLogs = [];

        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $item) {
                $storeItem = StoreItem::where('create_item_id', $item['product_id'])
                    ->where('store_id', $item['store_id'])
                    ->first();

                if (!$storeItem) {
                    $errors[] = "Product not found in selected store.";
                    continue;
                }

                $measurement = Measurement::find($item['unit_measurement']);
                if (!$measurement) {
                    $errors[] = "Invalid measurement unit.";
                    continue;
                }

                $quantityInPieces = StockUtil::getPieceQuivalent(
                    $measurement->name,
                    $storeItem->quantity_in_package ?? 1,
                    $item['quantity']
                );

                $availablePieces = StockUtil::getQuantityForRequest(
                    $item['product_id'],
                    $item['store_id']
                );

                if ($availablePieces < $quantityInPieces) {
                    $productName = $storeItem->createItem?->name ?? 'Unknown Product';
                    $errors[] = "Insufficient stock for {$productName}. Available: {$availablePieces} pieces, Requested: {$quantityInPieces} pieces.";
                    continue;
                }

                if ($storeItem->set_limit !== null && $item['quantity'] > $storeItem->set_limit) {
                    $productName = $storeItem->createItem?->name ?? 'Unknown';
                    $errors[] = "Sale exceeds limit ({$storeItem->set_limit}) for {$productName}.";
                    continue;
                }

                $auditLogs[] = [
                    'action_type' => 'sold',
                    'product_id' => $item['product_id'],
                    'user_id' => auth()->id(),
                    'store_id' => $item['store_id'],
                    'quantity_change' => -$quantityInPieces,
                    'previous_quantity' => $availablePieces,
                    'new_quantity' => $availablePieces - $quantityInPieces,
                    'reference_type' => 'SalesOrder',
                    'notes' => 'Sold via sales order',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'error' => implode(" | ", $errors)
                ], 400);
            }

            // Create Sales Order
            $salesOrderNumber = 'HGV-SO-' . strtoupper(uniqid());
            $salesOrder = SalesOrder::create([
                'sales_order_number' => $salesOrderNumber,
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'store_id' => $validated['store_id'],
                'user_id' => $validated['user_id'],
                'total_amount' => $validated['total_amount'],
                'payment_type' => $validated['payment']['payment_type'],
                'status' => $validated['payment']['payment_type'] === 'Credit' ? 'Credit Pending' : 'Pending',
            ]);

            // Save audit logs with reference
            foreach ($auditLogs as $log) {
                $log['reference_id'] = $salesOrder->id;
                ProductAudit::create($log);
            }

            // Create ItemSold records
            foreach ($validated['items'] as $item) {
                $measurement = Measurement::find($item['unit_measurement']);
                $quantityInPieces = StockUtil::getPieceQuivalent(
                    $measurement->name,
                    StoreItem::where('create_item_id', $item['product_id'])
                        ->where('store_id', $item['store_id'])
                        ->first()?->quantity_in_package ?? 1,
                    $item['quantity']
                );

                ItemSold::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_pieces' => $quantityInPieces,
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'amount' => $item['quantity'] * ($item['unit_price'] - ($item['discount'] ?? 0)),
                    'store_id' => $item['store_id'],
                    'unit_measurement' => $item['unit_measurement'],
                    'sales_date' => now(),
                ]);
            }

            // ======================================================
            // POST ACCOUNTING ENTRIES
            // ======================================================
            $this->postAccountingEntries(
                $salesOrder->id,
                $validated['customer_id'],
                $validated['total_amount'],
                $validated['payment']['payment_type'],
                $salesOrder->created_at
            );

            DB::commit();

            return response()->json([
                'message' => 'Sales Order Created Successfully',
                'data' => new SalesOrderResource($salesOrder)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sales Order Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'error' => 'An error occurred while creating the sales order.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Post accounting entries for sales order
     */
    private function postAccountingEntries(
        string $saleId,
        string $customerId,
        float $totalAmount,
        string $paymentType,
        $saleDate
    ) {
        // Get account IDs from chart of accounts
        $cashAccount = DB::table('ledger_accounts')
            ->where('account_code', '1000')
            ->value('account_id');

        $receivableAccount = DB::table('ledger_accounts')
            ->where('account_code', '1020')
            ->value('account_id');

        $salesAccount = DB::table('ledger_accounts')
            ->where('account_code', '4000')
            ->value('account_id');

        // Validate accounts exist
        if (!$cashAccount || !$receivableAccount || !$salesAccount) {
            throw new \Exception('Required accounting accounts not found in chart of accounts');
        }

        $transactionId = Str::uuid()->toString();
        $journalId = Str::uuid()->toString();

        // Determine description based on payment type
        $description = ($paymentType === 'Credit')
            ? "Credit Sale #{$saleId}"
            : "{$paymentType} Sale #{$saleId}";

        // 1. Create Transaction Record
        DB::table('transactions')->insert([
            'transaction_id' => $transactionId,
            'transaction_type' => 'SALE',
            'reference_id' => $saleId,
            'transaction_date' => date('Y-m-d', strtotime($saleDate)),
            'total_amount' => $totalAmount,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Journal Entry Header
        DB::table('journal_entry')->insert([
            'journal_id' => $journalId,  // Use journal_id instead of id
            'transaction_id' => $transactionId,
            'entry_date' => date('Y-m-d', strtotime($saleDate)),
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Journal Lines (Double Entry)

        // DEBIT: Cash or Accounts Receivable
        if ($paymentType === 'Credit') {
            // Debit Accounts Receivable
            DB::table('journal_lines')->insert([
                'line_id' => Str::uuid()->toString(),
                'journal_id' => $journalId,
                'account_id' => $receivableAccount,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                // 'description' => "A/R - Customer ID: {$customerId}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Debit Cash/Bank
            DB::table('journal_lines')->insert([
                'line_id' => Str::uuid()->toString(),
                'journal_id' => $journalId,
                'account_id' => $cashAccount,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                // 'description' => "{$paymentType} Receipt",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // CREDIT: Sales Revenue
        DB::table('journal_lines')->insert([
            'line_id' => Str::uuid()->toString(),
            'journal_id' => $journalId,
            'account_id' => $salesAccount,
            'debit_amount' => 0,
            'credit_amount' => $totalAmount,
            // 'description' => 'Sales Revenue',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Post to General Ledger
        $journalLines = DB::table('journal_lines')
            ->where('journal_id', $journalId)
            ->get();

        foreach ($journalLines as $line) {
            DB::table('ledger_postings')->insert([
                'posting_id' => Str::uuid()->toString(),
                'line_id' => $line->line_id,
                'account_id' => $line->account_id,
                'posting_date' => date('Y-m-d', strtotime($saleDate)),
                'debit_amount' => $line->debit_amount,
                'credit_amount' => $line->credit_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }



    public function edit($id)
    {


        // Check if sales order is linked to any releases
        $salesOrder = SalesOrder::findOrFail($id);
        $hasRelease = SalesReceipt::where('sales_order_id', $salesOrder->id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('releases')
                    ->whereColumn('releases.sales_receipt_id', 'sales_receipts.id');
            })
            ->exists();

        if ($hasRelease) {
            return response()->json([
                'message' => 'Cannot edit sales order that has been released'
            ], 403);
        }

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

            $hasRelease = SalesReceipt::where('sales_order_id', $salesOrder->id)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('releases')
                        ->whereColumn('releases.sales_receipt_id', 'sales_receipts.id');
                })
                ->exists();

            if ($hasRelease) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Cannot update sales order that has been released'
                ], 403);
            }

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

            // Find related sales receipts
            $salesReceipts = SalesReceipt::where('sales_order_id', $salesOrder->id)->get();

            foreach ($salesReceipts as $receipt) {
                $receipt->amount_paid = $salesOrder->total_amount;
                $receipt->total_amount = $salesOrder->total_amount;

                // If payment_detail is a JSON column, update it here.
                // Example: set all to new total_amount as cash (adjust as needed for your logic)
                $receipt->payment_detail = json_encode([
                    [
                        'payment_type' => $salesOrder->payment_type,
                        'amount' => $salesOrder->total_amount
                    ]
                ]);
                $receipt->save();
            }

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
    // public function cancel($id)
    // {
    //     // Start a database transaction
    //     DB::beginTransaction();

    //     try {
    //         // Find the sales order
    //         $salesOrder = SalesOrder::with('itemSold')->findOrFail($id);

    //         // Check if the sales order is already canceled
    //         if ($salesOrder->status === 'Canceled') {
    //             return response()->json(['message' => 'Sales Order is already canceled.'], 400);
    //         }

    //         // Loop through items in the sales order and restore stock
    //         foreach ($salesOrder->itemSold as $itemSold) {
    //             $storeItem = StoreItem::where('create_item_id', $itemSold->product_id)
    //                 ->where('store_id', $salesOrder->store_id)
    //                 ->first();

    //             if ($storeItem) {
    //                 // Calculate previous quantity using StockUtil
    //                 $previousQuantity = StockUtil::getQuantityForRequest($itemSold->product_id, $itemSold->store_id);
    //                 $quantityChange = $itemSold->quantity_pieces;
    //                 $newQuantity = $previousQuantity + $quantityChange;

    //                 // Optionally update the storeItem's available quantity here if needed
    //                 // $storeItem->quantity_available = $newQuantity;
    //                 // $storeItem->save();

    //                 // Log the restoration in ProductAudit
    //                 ProductAudit::create([
    //                     'action_type' => 'restored',
    //                     'product_id' => $itemSold->product_id,
    //                     'user_id' => auth()->id(),
    //                     'quantity_change' => $quantityChange,
    //                     'previous_quantity' => $previousQuantity,
    //                     'new_quantity' => $newQuantity,
    //                     'reference_type' => 'SalesOrder',
    //                     'reference_id' => $salesOrder->id,
    //                     'store_id' => $salesOrder->store_id,
    //                     'notes' => 'Stock restored due to order cancellation'
    //                 ]);
    //             } else {
    //                 Log::warning("StoreItem for product {$itemSold->product_id} not found in store {$salesOrder->store_id}");
    //             }
    //         }

    //         // Update the order status to 'Canceled' instead of deleting it
    //         $salesOrder->update(['status' => 'Canceled']);

    //         // Commit the transaction
    //         DB::commit();

    //         return response()->json(['message' => 'Sales Order Canceled Successfully.'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error canceling sales order: ' . $e->getMessage());
    //         return response()->json(['message' => 'An error occurred while canceling the order.'], 500);
    //     }
    // }

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

                if ($storeItem) {
                    // Calculate previous quantity using StockUtil
                    $previousQuantity = StockUtil::getQuantityForRequest($itemSold->product_id, $itemSold->store_id);
                    $quantityChange = $itemSold->quantity_pieces;
                    $newQuantity = $previousQuantity + $quantityChange;

                    // Log the restoration in ProductAudit
                    ProductAudit::create([
                        'action_type' => 'restored',
                        'product_id' => $itemSold->product_id,
                        'user_id' => auth()->id(),
                        'quantity_change' => $quantityChange,
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $newQuantity,
                        'reference_type' => 'SalesOrder',
                        'reference_id' => $salesOrder->id,
                        'store_id' => $salesOrder->store_id,
                        'notes' => 'Stock restored due to order cancellation'
                    ]);
                } else {
                    Log::warning("StoreItem for product {$itemSold->product_id} not found in store {$salesOrder->store_id}");
                }
            }

            // ======================================================
            // REVERSE ACCOUNTING ENTRIES
            // ======================================================
            $this->reverseAccountingEntries($salesOrder->id, $salesOrder->created_at);

            // Update the order status to 'Canceled' instead of deleting it
            $salesOrder->update(['status' => 'Canceled']);

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Sales Order Canceled Successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error canceling sales order: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while canceling the order.'], 500);
        }
    }

    /**
     * Reverse accounting entries for a canceled sales order
     */
    private function reverseAccountingEntries(string $saleId, $saleDate)
    {
        // Find the original transaction
        $transaction = DB::table('transactions')
            ->where('reference_id', $saleId)
            ->where('transaction_type', 'SALE')
            ->first();

        if (!$transaction) {
            Log::warning("No accounting transaction found for sale ID: {$saleId}");
            return; // Exit gracefully if no transaction exists
        }

        // Find the journal entry
        $journalEntry = DB::table('journal_entry')
            ->where('transaction_id', $transaction->transaction_id)
            ->first();

        if (!$journalEntry) {
            Log::warning("No journal entry found for transaction ID: {$transaction->transaction_id}");
            return;
        }

        // Get all journal lines for this entry
        $journalLines = DB::table('journal_lines')
            ->where('journal_id', $journalEntry->journal_id)
            ->get();

        if ($journalLines->isEmpty()) {
            Log::warning("No journal lines found for journal ID: {$journalEntry->journal_id}");
            return;
        }

        // Create reversal transaction
        $reversalTransactionId = Str::uuid()->toString();
        $reversalJournalId = Str::uuid()->toString();

        // 1. Create Reversal Transaction Record
        DB::table('transactions')->insert([
            'transaction_id' => $reversalTransactionId,
            'transaction_type' => 'SALE_REVERSAL',
            'reference_id' => $saleId,
            'transaction_date' => date('Y-m-d'),
            'total_amount' => $transaction->total_amount,
            'description' => "Reversal: {$transaction->description}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Reversal Journal Entry Header
        DB::table('journal_entry')->insert([
            'journal_id' => $reversalJournalId,
            'transaction_id' => $reversalTransactionId,
            'entry_date' => date('Y-m-d'),
            'description' => "Reversal: {$journalEntry->description}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Reversed Journal Lines (swap debit and credit)
        foreach ($journalLines as $line) {
            $reversalLineId = Str::uuid()->toString();

            DB::table('journal_lines')->insert([
                'line_id' => $reversalLineId,
                'journal_id' => $reversalJournalId,
                'account_id' => $line->account_id,
                'debit_amount' => $line->credit_amount,  // Swap credit to debit
                'credit_amount' => $line->debit_amount,  // Swap debit to credit
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Post Reversal to General Ledger
            DB::table('ledger_postings')->insert([
                'posting_id' => Str::uuid()->toString(),
                'line_id' => $reversalLineId,
                'account_id' => $line->account_id,
                'posting_date' => date('Y-m-d'),
                'debit_amount' => $line->credit_amount,  // Reversed amounts
                'credit_amount' => $line->debit_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Log::info("Accounting entries reversed for sale ID: {$saleId}");
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

        // --- Prevent double returns ---
        $salesReceipt = SalesReceipt::where('sales_receipt_number', $validated['sales_receipt_number'])->firstOrFail();
        $existingReturn = ReturnItem::where('sales_receipt_id', $salesReceipt->id)->exists();
        if ($existingReturn) {
            return response()->json([
                'success' => false,
                'message' => 'This receipt has already been processed for returns.',
            ], 400);
        }
        // --- End double return check ---

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

                // Calculate return amount using discounted unit price
                $discount = $itemSold->discount ?? 0;
                $discountedUnitPrice = max(0, ($itemData['unit_price'] ?? 0) - $discount);
                $returnAmount = $itemData['quantity_returned'] * $discountedUnitPrice;
                $totalReturnAmount += $returnAmount;

                // Update item_sold record
                $returnDetails = new ReturnDetails();
                $returnDetails->return_id = $return->id;
                $returnDetails->product_id = $itemData['product_id'];
                $returnDetails->return_quantity = $itemData['quantity_returned'];
                $returnDetails->item_sold_id = $itemData['item_sold_id'];
                $returnDetails->unit_price = $itemData['unit_price'];
                $returnDetails->discount = $discount; // persist discount at the time of sale
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
            // ProductAudit::create([
            //     'action_type' => 'returned',
            //     'product_id' => $itemData['product_id'],
            //     'user_id' => auth()->id(),
            //     'quantity_change' => $itemData['quantity_returned'],
            //     'reference_type' => 'ReturnItem',
            //     'reference_id' => $return->id,
            //     'notes' => 'Product returned'
            // ]);
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






    public function salesReport(Request $request): JsonResponse
    {
        // Validate request parameters
        $validated = $request->validate([
            'store_id' => 'required|string|exists:stores,id',
            'product_id' => 'nullable|integer|exists:create_items,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // Extract validated parameters
        $storeId = $validated['store_id'];
        $productId = $validated['product_id'] ?? null;
        $fromDate = Carbon::parse($validated['from_date'])->startOfDay();
        $toDate = Carbon::parse($validated['to_date'])->endOfDay();

        // Build the query
        $query = ItemSold::query()
            ->join('create_items', 'item_solds.product_id', '=', 'create_items.id')
            ->join('sales_orders', 'item_solds.sales_order_id', '=', 'sales_orders.id')
            ->join('users', 'sales_orders.user_id', '=', 'users.id')
            ->where('item_solds.store_id', $storeId)
            ->whereBetween('item_solds.sales_date', [$fromDate, $toDate])
            ->whereNull('item_solds.deleted_at');

        // Apply product filter if provided
        if ($productId) {
            $query->where('item_solds.product_id', $productId);
        }

        // Select fields and group by
        $selectFields = [
            'item_solds.product_id',
            'create_items.name as product_name',
            DB::raw('SUM(item_solds.quantity) as total_quantity'),
            DB::raw('SUM(item_solds.amount) as total_amount'),
            DB::raw('COUNT(item_solds.id) as total_transactions')
        ];

        // Include user name only if a product is selected
        if ($productId) {
            $selectFields[] = 'users.name as user_name';
            $query->groupBy('item_solds.product_id', 'create_items.name', 'users.name');
        } else {
            $query->groupBy('item_solds.product_id', 'create_items.name');
        }

        // Execute query
        $results = $query->select($selectFields)
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Transform the results to match frontend expectations
        $transformedResults = $results->map(function ($item) use ($productId) {
            $result = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'total_quantity' => (int) $item->total_quantity,
                'total_amount' => (float) $item->total_amount,
                'total_transactions' => (int) $item->total_transactions
            ];

            // Include user_name only if product_id is provided
            if ($productId && isset($item->user_name)) {
                $result['user_name'] = $item->user_name;
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => $transformedResults,
            'summary' => [
                'total_products' => $transformedResults->count(),
                'total_quantity_sold' => $transformedResults->sum('total_quantity'),
                'total_amount' => $transformedResults->sum('total_amount'),
                'period' => [
                    'from' => $fromDate->format('Y-m-d'),
                    'to' => $toDate->format('Y-m-d')
                ]
            ]
        ]);
    }

    /**
     * Get stores with their sold items for the authenticated user's branch
     */
    public function myStoreItemSold(Request $request): JsonResponse
    {
        try {
            // Get stores for the authenticated user's branch
            $stores = Store::where('branch_id', auth()->user()->branch_id)
                ->whereIn('store_type_id', [1, 2])
                ->with(['itemSolds' => function ($query) {
                    $query->with('product')
                        ->whereNull('deleted_at')
                        ->select('id', 'product_id', 'store_id', 'sales_date', 'quantity', 'amount');
                }])
                ->get();

            // Transform the data to match frontend expectations
            $transformedStores = $stores->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'branch_id' => $store->branch_id,
                    'store_type_id' => $store->store_type_id,
                    'itemSolds' => $store->itemSolds->map(function ($itemSold) {
                        return [
                            'id' => $itemSold->id,
                            'product_id' => $itemSold->product_id,
                            'store_id' => $itemSold->store_id,
                            'sales_date' => $itemSold->sales_date,
                            'quantity' => $itemSold->quantity,
                            'amount' => $itemSold->amount,
                            'product' => $itemSold->product ? [
                                'id' => $itemSold->product->id,
                                'name' => $itemSold->product->name
                            ] : null
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedStores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stores and items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique products sold in a specific store
     */
    public function getStoreProducts(Request $request, $storeId): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|string|exists:stores,id'
        ]);

        try {
            $products = ItemSold::join('create_items', 'item_solds.product_id', '=', 'create_items.id')
                ->where('item_solds.store_id', $storeId)
                ->whereNull('item_solds.deleted_at')
                ->select('create_items.id', 'create_items.name')
                ->distinct()
                ->orderBy('create_items.name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching store products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales summary for dashboard
     */
    public function getSalesSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|string|exists:stores,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $storeId = $validated['store_id'];
        $fromDate = Carbon::parse($validated['from_date'])->startOfDay();
        $toDate = Carbon::parse($validated['to_date'])->endOfDay();

        try {
            $summary = ItemSold::where('store_id', $storeId)
                ->whereBetween('sales_date', [$fromDate, $toDate])
                ->whereNull('deleted_at')
                ->select([
                    DB::raw('COUNT(DISTINCT product_id) as unique_products'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('COUNT(id) as total_transactions'),
                    DB::raw('AVG(amount) as average_transaction_amount')
                ])
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'unique_products' => (int) $summary->unique_products,
                    'total_quantity' => (int) $summary->total_quantity,
                    'total_amount' => (float) $summary->total_amount,
                    'total_transactions' => (int) $summary->total_transactions,
                    'average_transaction_amount' => (float) $summary->average_transaction_amount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sales summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
