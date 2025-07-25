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
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReturnItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'store_id' => 'nullable|integer|exists:stores,id',
            'branch_id' => 'nullable|integer|exists:stores,branch_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'product_id' => 'nullable|integer|exists:create_items,id',
            'return_status' => 'nullable|string|in:approved,pending,rejected', // Changed from return_status to status
        ]);

        // Extract validated parameters
        $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $productId = $validated['product_id'] ?? null;
        $status = $validated['status'] ?? 'approved'; // Default to approved

        // Start building the query with all returns
        $query = ReturnItem::with([
            'customer',
            'store',
            'user',
            'branch',
            'approvedBy',
            'returnDetails.product',
            'returnDetails.measurement',
        ]);

        // Apply store filter if provided
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Apply branch filter if provided
        if ($branchId) {
            $query->whereHas('store', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }

        // Apply product filter if provided
        if ($productId) {
            $query->whereHas('returnDetails', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            });
        }

        // Apply status filter - only if status is provided
        $query->where('return_status', $status);

        // Apply date range filters if provided (using return_date instead of created_at)
        if ($fromDate || $toDate) {
            // Convert from_date and to_date to Carbon instances only if provided
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            // Apply date filter for the selected range
            if ($fromDate && $toDate) {
                // Both from_date and to_date are provided
                $query->whereBetween('return_date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                // Only from_date is provided
                $query->where('return_date', '>=', $fromDate);
            } elseif ($toDate) {
                // Only to_date is provided
                $query->where('return_date', '<=', $toDate);
            }
        }

        // Order by recent first
        $query->orderBy('created_at', 'desc');

        // Fetch the results and format them
        $returns = $query->get();

        return response()->json($returns);
    }
    public function store(ReturnItemStoreRequest $request): ReturnItemResource
    {
        $returnItem = ReturnItem::create($request->validated());

        return new ReturnItemResource($returnItem);
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
                    return -$detail->return_quantity * $detail->unit_price;
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
                    return $detail->return_quantity * $detail->unit_price;
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

        // Calculate return amount
        $returnAmount = $returnItem->returnDetails->sum(function ($detail) {
            return $detail->return_quantity * $detail->unit_price;
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
}
