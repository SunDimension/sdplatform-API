<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnItemStoreRequest;
use App\Http\Requests\ReturnItemUpdateRequest;
use App\Http\Resources\ReturnItemCollection;
use App\Http\Resources\ReturnItemResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CreditTransaction;
use App\Models\ItemSold;
use App\Models\SalesOrder;
use App\Models\PostInflow;
use App\Models\ReturnItem;
use App\Models\TransactionJournalEntry;
use App\Services\AccountingEntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReturnItemController extends Controller
{
    protected $accountingEntryService;

    public function __construct(AccountingEntryService $accountingEntryService)
    {
        $this->accountingEntryService = $accountingEntryService;
    }

    public function index(Request $request): JsonResponse
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'store_id' => 'nullable|integer|exists:stores,id',
            'branch_id' => 'nullable|integer|exists:stores,branch_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'product_id' => 'nullable|integer|exists:create_items,id',
            'return_status' => 'nullable|string|in:approved,pending,rejected',
        ]);

        // Extract validated parameters
        $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $productId = $validated['product_id'] ?? null;
        $returnStatus = $validated['return_status'] ?? 'approved'; // Default to 'approved'

     

        // Start building the query with all returns
        $query = ReturnItem::with([
            'customer',
            'store',
            'user',
            'branch',
            'approvedBy',
            'returnDetails.product',
            'returnDetails.measurement',
        ])->where('return_status', $returnStatus); // Always filter by return_status

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($branchId) {
            $query->whereHas('store', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }

        if ($productId) {
            $query->whereHas('returnDetails', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            });
        }

        // Apply date range filters if provided (using return_date)
        if ($fromDate || $toDate) {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('return_date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('return_date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('return_date', '<=', $toDate);
            }
        }

        $query->orderBy('created_at', 'desc');
        $returns = $query->get();

        return new ReturnItemCollection($returns);
    }
    public function store(ReturnItemStoreRequest $request): ReturnItemResource
    {
        return DB::transaction(function () use ($request) {
            $returnItem = ReturnItem::create($request->validated());

            // Generate accounting entries for the return item
            try {
                $this->accountingEntryService->generateReturnItemEntries($returnItem);
            } catch (\Exception $e) {
                Log::error('Failed to generate accounting entries for return item', [
                    'return_item_id' => $returnItem->id,
                    'error' => $e->getMessage()
                ]);
                // Don't throw the exception to avoid rolling back the main transaction
            }

            return new ReturnItemResource($returnItem);
        });
    }

    public function show(Request $request, ReturnItem $returnItem): ReturnItemResource
    {
        return new ReturnItemResource($returnItem);
    }

    public function update(ReturnItemUpdateRequest $request, ReturnItem $returnItem): ReturnItemResource
    {
        $returnItem->update($request->validated());

        return new ReturnItemResource($returnItem);
    }

    public function pending(Request $request): ReturnItemCollection
    {
        $user = auth()->user();
        Log::info('Fetching pending invoice for branch.', ['branch_id' => $user->branch_id, 'user_id' => $user->id]);

        $returnItem = ReturnItem::where('return_status', 'pending')
            ->where('branch_id', $user->branch_id)
            ->with(['returnDetails', 'branch', 'customer', 'user']) // Eager load all necessary relationships
            ->get();

        Log::info('Fetched pending bank remittances:', ['count' => $returnItem->count()]);

        return new ReturnItemCollection($returnItem);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required']
        ]);


        $returnItem = ReturnItem::findOrFail($validated['id']);
        $returnItem->approval_comment = $validated['comment'];
        $returnItem->return_status = $validated['status'];
        $returnItem->approved_by = auth()->user()->id;
        $returnItem->approved_at = now();
        $returnItem->save();

        if ($validated['status'] == 'Approved') {

            $salesReceipt = $returnItem->salesReceipt;
            $salesOrder = $salesReceipt ? SalesOrder::find($salesReceipt->sales_order_id) : null;

            foreach ($returnItem->returnDetails as $returnDetail) {
                // Find the ItemSold record matching product_id and store_id
                $itemSold = ItemSold::where('product_id', $returnDetail->product_id)
                    ->where('store_id', $returnItem->store_id)
                    ->where('sales_order_id', $salesOrder ? $salesOrder->id : null)
                    ->first();
                if ($itemSold) {
                    // Reduce quantity_sold or update returned_quantity as needed
                    $itemSold->return_quantity = max(0, $itemSold->quantity - $returnDetail->return_quantity);
                    $itemSold->return_quantity_pieces = max(0, $itemSold->quantity_pieces - $returnDetail->return_quantity_pieces);
                    $itemSold->save();
                }

                // --- ProductAudit for return approval ---
                $previousQuantity = \App\Classes\StockUtil::getQuantityForRequest($returnDetail->product_id, $returnItem->store_id);
                $quantityChange = $returnDetail->return_quantity_pieces;
                $newQuantity = $previousQuantity + $quantityChange;

                \App\Models\ProductAudit::create([
                    'action_type' => 'returned',
                    'product_id' => $returnDetail->product_id,
                    'user_id' => auth()->id(),
                    'quantity_change' => $quantityChange,
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => 'ReturnItem',
                    'reference_id' => $returnItem->id,
                    'store_id' => $returnItem->store_id,
                    'notes' => 'Product returned and approved'
                ]);
                // --- End ProductAudit ---
            }

            if ($salesOrder && $salesOrder->payment_type === 'Credit') {
                $data = new CreditTransaction();
                $data->customer_id = $returnItem->customer_id;
                $data->branch_id = $returnItem->branch_id;
                $data->sales_order_id = $salesOrder->id;

                foreach ($returnItem->returnDetails as $returnDetail) {
                    // Find the ItemSold record matching product_id and store_id
                    $itemSold = ItemSold::where('product_id', $returnDetail->product_id)
                        ->where('store_id', $returnItem->store_id)
                        ->where('sales_order_id', $salesOrder->id)
                        ->first();
                    if ($itemSold) {
                        // Reduce quantity_sold or update returned_quantity as needed
                        $itemSold->return_quantity = max(0, $itemSold->quantity - $returnDetail->return_quantity);
                        $itemSold->return_quantity_pieces = max(0, $itemSold->quantity_pieces - $returnDetail->return_quantity_pieces);
                        // Optionally, track returned_quantity if your schema supports it:
                        // $itemSold->returned_quantity = ($itemSold->returned_quantity ?? 0) + $returnDetail->return_quantity;
                        $itemSold->save();
                    }
                }

                $data->type = 'Return Adjustment';
                $totalamount = $salesOrder->itemSold->sum(function ($detail) {
                    return ($detail->quantity - $detail->return_quantity) * ($detail->unit_price - $detail->discount);
                });

                $data->amount = $returnItem->returnDetails->sum(function ($detail) {
                    $itemSold = $detail->item_sold_id ? \App\Models\ItemSold::find($detail->item_sold_id) : null;
                    $discount = $detail->discount ?? ($itemSold->discount ?? 0);
                    $unitPrice = $detail->unit_price ?? 0;
                    return -$detail->return_quantity * max(0, $unitPrice - $discount);
                });
                $salesOrder->total_amount = $totalamount;
                $salesOrder->save();

                // (price.value - discount.value) * quantity.value,
                $customer = Customer::findOrFail($returnItem->customer_id);
                $creditSum = CreditTransaction::where('customer_id', $returnItem->customer_id)->sum('amount');
                $data->credit_balance_before = $customer->credit_limit - $creditSum;
                $data->credit_balance_after = $customer->credit_limit - $creditSum - $data->amount;
                $data->credit_limit = $customer->credit_limit;

                // $data->credit_balance_after = $salesOrder->credit_balance - $data->amount;
                // $data->transaction_date = now();
                $data->created_by = auth()->user()->id;
                $data->notes = "Credit adjustment for return #{$returnItem->id}";
                $data->save();

                
            } else {

                $data = new PostInflow();
                $data->inflow_status = 3; // claimed status
                $data->customer_id = $returnItem->customer_id;
                $data->amount = $returnItem->returnDetails->sum(function ($detail) {
                    $itemSold = $detail->item_sold_id ? \App\Models\ItemSold::find($detail->item_sold_id) : null;
                    $discount = $detail->discount ?? ($itemSold->discount ?? 0);
                    $unitPrice = $detail->unit_price ?? 0;
                    return $detail->return_quantity * max(0, $unitPrice - $discount);
                });
                $data->narration = "Return of items";
                $data->inflow_date = now();
                $data->save();
            }
        }


        return new ReturnItemResource($returnItem);
    }





    // In your ReturnItemController's get method
    public function get($id): ReturnItemResource
    {
        $returnItem = ReturnItem::with([
            'returnDetails.product',
            'salesReceipt.salesOrder.itemSold.product', // or 'items' depending on your relationship name
            'salesReceipt.salesOrder.itemSold.store',
            'salesReceipt.salesOrder.itemSold.measurement',
            'branch',
            'customer',
            'user'
        ])->findOrFail($id);

        // Debug output
        Log::info('Sales Receipt Loaded', [
            'loaded' => $returnItem->relationLoaded('salesReceipt'),
            'exists' => $returnItem->salesReceipt ? 'yes' : 'no',
            'id' => $returnItem->sales_receipt_id
        ]);

        return new ReturnItemResource($returnItem);
    }
    public function destroy($id)
    {

        ReturnItem::destroy($id);


        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function pendingCredit(Request $request): ReturnItemCollection
    {
        $user = auth()->user();

        $returns = ReturnItem::where('return_status', 'pending')
            ->where('branch_id', $user->branch_id)
            ->whereHas('salesReceipt.salesOrder', function ($query) {
                $query->where('payment_type', 'Credit');
            })
            ->with([
                'returnDetails.product',
                'branch',
                'customer',
                'user',
                'salesReceipt.salesOrder'
            ])
            ->get();

        return new ReturnItemCollection($returns);
    }

    /**
     * Approve credit returns and update existing credit transaction
     */
    public function approveCreditReturn(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable', 'string'],
            'id' => ['required', 'exists:return_items,id']
        ]);

        $returnItem = ReturnItem::with([
            'returnDetails',
            'salesReceipt.salesOrder',
            'customer',
            'salesReceipt.salesOrder.creditTransactions' // Load existing credit transactions
        ])->findOrFail($validated['id']);

        // Verify this is a credit purchase
        if (
            !$returnItem->salesReceipt ||
            !$returnItem->salesReceipt->salesOrder ||
            $returnItem->salesReceipt->salesOrder->payment_type !== 'Credit'
        ) {
            return response()->json([
                'message' => 'This return is not for a credit purchase'
            ], 400);
        }

        // Calculate return amount using discounted unit price
        $returnAmount = $returnItem->returnDetails->sum(function ($detail) {
            $itemSold = $detail->item_sold_id ? \App\Models\ItemSold::find($detail->item_sold_id) : null;
            $discount = $detail->discount ?? ($itemSold->discount ?? 0);
            $unitPrice = $detail->unit_price ?? 0;
            return $detail->return_quantity * max(0, $unitPrice - $discount);
        });

        DB::beginTransaction();
        try {
            // Find the original credit transaction
            $creditTransaction = $returnItem->salesReceipt->salesOrder
                ->creditTransactions
                ->where('type', 'credit')
                ->first();

            if (!$creditTransaction) {
                throw new \Exception('Original credit transaction not found');
            }

            // Update return status
            $returnItem->update([
                'return_status' => 'Approved',
                'approval_comment' => $validated['comment'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'credit_transaction_id' => $creditTransaction->id
            ]);

            $customer = $returnItem->customer;
            $previousBalance = $customer->credit_balance;

            // Update the original credit transaction
            $creditTransaction->update([
                'amount' => $creditTransaction->amount - $returnAmount,
                'credit_balance_after' => $previousBalance - $returnAmount,
                'notes' => ($creditTransaction->notes ? $creditTransaction->notes . "\n" : '') .
                    "Adjusted by -{$returnAmount} for return #{$returnItem->id}"
            ]);

            // Update customer's credit balance
            $customer->credit_balance = $previousBalance - $returnAmount;
            $customer->save();

            DB::commit();

            return new ReturnItemResource($returnItem->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving credit return', [
                'error' => $e->getMessage(),
                'return_id' => $returnItem->id
            ]);

            return response()->json([
                'message' => 'Failed to approve credit return',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate accounting entries for a specific return item
     */
    public function generateAccountingEntries($id)
    {
        $returnItem = ReturnItem::with(['returnDetails', 'salesReceipt.salesOrder'])->findOrFail($id);

        // Check if accounting entries already exist
        $existingEntries = TransactionJournalEntry::where('description', 'LIKE', "%Return Item #{$returnItem->id}%")->get();
        
        if ($existingEntries->count() > 0) {
            return response()->json([
                'message' => 'Accounting entries already exist for this return item',
                'existing_entries' => $existingEntries
            ], 400);
        }

        try {
            $journalEntry = $this->accountingEntryService->generateReturnItemEntries($returnItem);
            
            return response()->json([
                'message' => 'Accounting entries generated successfully',
                'journal_entry' => $journalEntry
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate accounting entries for return item', [
                'return_item_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to generate accounting entries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate accounting entries for multiple return items
     */
    public function generateBulkAccountingEntries(Request $request)
    {
        $validated = $request->validate([
            'return_item_ids' => 'required|array',
            'return_item_ids.*' => 'integer|exists:return_items,id'
        ]);

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($validated['return_item_ids'] as $id) {
            try {
                $returnItem = ReturnItem::with(['returnDetails', 'salesReceipt.salesOrder'])->find($id);
                
                // Check if accounting entries already exist
                $existingEntries = TransactionJournalEntry::where('description', 'LIKE', "%Return Item #{$returnItem->id}%")->get();
                
                if ($existingEntries->count() > 0) {
                    $results[] = [
                        'id' => $id,
                        'status' => 'skipped',
                        'message' => 'Accounting entries already exist'
                    ];
                    continue;
                }

                $journalEntry = $this->accountingEntryService->generateReturnItemEntries($returnItem);
                
                $results[] = [
                    'id' => $id,
                    'status' => 'success',
                    'journal_entry_id' => $journalEntry->id
                ];
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Failed to generate accounting entries for return item', [
                    'return_item_id' => $id,
                    'error' => $e->getMessage()
                ]);

                $results[] = [
                    'id' => $id,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                $failureCount++;
            }
        }

        return response()->json([
            'message' => "Bulk accounting entries generation completed. Success: {$successCount}, Failures: {$failureCount}",
            'results' => $results,
            'summary' => [
                'total' => count($validated['return_item_ids']),
                'success' => $successCount,
                'failure' => $failureCount
            ]
        ]);
    }

    /**
     * Get accounting entries for a specific return item
     */
    public function getAccountingEntries($id)
    {
        $returnItem = ReturnItem::findOrFail($id);

        $journalEntries = TransactionJournalEntry::where('description', 'LIKE', "%Return Item #{$returnItem->id}%")
            ->with(['details.account', 'details.journalType'])
            ->get();

        return response()->json([
            'return_item_id' => $id,
            'journal_entries' => $journalEntries
        ]);
    }
}
