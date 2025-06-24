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
use App\Models\SalesOrder;
use App\Models\PostInflow;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReturnItemController extends Controller
{
    public function index(Request $request): ReturnItemCollection
    {
        $returnItem = ReturnItem::all();

        return new ReturnItemCollection($returnItem);
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
           
            if($salesOrder && $salesOrder->payment_type === 'Credit'){
                $data = new CreditTransaction();
                $data->customer_id = $returnItem->customer_id;
                $data->branch_id = $returnItem->branch_id;
                $data->sales_order_id = $salesOrder->id;
                $data->type = 'Return Adjustment';
                $data->amount = $returnItem->returnDetails->sum(function ($detail) {
                    return - $detail->return_quantity * $detail->unit_price;
                });
                $data->credit_balance_before = $salesOrder->credit_balance;
                // $data->credit_balance_after = $salesOrder->credit_balance - $data->amount;
                // $data->transaction_date = now();
                $data->created_by = auth()->user()->id;
                $data->notes = "Credit adjustment for return #{$returnItem->id}";
                $data->save();
            }
            else{

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



    // public function approve(Request $request)
    // {
    //     $validated = $request->validate([
    //         'comment' => ['nullable'],
    //         'status' => ['required', 'string'],
    //         'id' => ['required']
    //     ]);

    //     $returnItem = ReturnItem::findOrFail($validated['id']);
    //     $returnItem->approval_comment = $validated['comment'];
    //     $returnItem->return_status = $validated['status'];
    //     $returnItem->approved_by = auth()->user()->id;
    //     $returnItem->approved_at = now();
    //     $returnItem->save();

    //     if ($validated['status'] == 'Approved') {
    //         // Get the original sales order to check payment type
    //         $salesReceipt = $returnItem->salesReceipt;
    //         $salesOrder = $salesReceipt ? SalesOrder::find($salesReceipt->sales_order_id) : null;

    //         $returnAmount = $returnItem->returnDetails->sum(function ($detail) {
    //             return $detail->return_quantity * $detail->unit_price;
    //         });

    //         if ($salesOrder && $salesOrder->payment_type === 'Credit') {
    //             // For credit purchases, reduce customer's credit balance
    //             $customer = Customer::find($returnItem->customer_id);
    //             if ($customer) {
    //                 $previousBalance = $customer->credit_balance;
    //                 $customer->credit_balance = max(0, $customer->credit_balance - $returnAmount);
    //                 $customer->save();

    //                 // Find the original credit transaction
    //                 $creditTransaction = CreditTransaction::where('sales_order_id', $salesOrder->id)
    //                     ->where('type', 'credit')
    //                     ->first();

    //                 if ($creditTransaction) {
    //                     // Update the existing credit transaction
    //                     $creditTransaction->update([
    //                         'amount' => $creditTransaction->amount - $returnAmount,
    //                         'credit_balance_before' => $creditTransaction->credit_balance_before - $returnAmount,
    //                         'notes' => $creditTransaction->notes . "\nAdjusted by -{$returnAmount} for return #{$returnItem->id}"
    //                     ]);

    //                     // Link the return item to the credit transaction
    //                     $returnItem->credit_transaction_id = $creditTransaction->id;
    //                     $returnItem->save();
    //                 } else {
    //                     // Fallback: Create new adjustment if original not found
    //                     $creditTransaction = new CreditTransaction();
    //                     $creditTransaction->customer_id = $returnItem->customer_id;
    //                     $creditTransaction->branch_id = $returnItem->branch_id;
    //                     $creditTransaction->sales_order_id = $salesOrder->id;
    //                     $creditTransaction->type = 'return_adjustment';
    //                     $creditTransaction->amount = $returnAmount;
    //                     $creditTransaction->credit_balance_before = $previousBalance;
    //                     $creditTransaction->credit_balance_after = $previousBalance - $returnAmount;
    //                     $creditTransaction->transaction_date = now();
    //                     $creditTransaction->created_by = auth()->user()->id;
    //                     $creditTransaction->notes = "Credit adjustment for return #{$returnItem->id}";
    //                     $creditTransaction->save();

    //                     $returnItem->credit_transaction_id = $creditTransaction->id;
    //                     $returnItem->save();
    //                 }
    //             }
    //         } else {
    //             // For cash purchases, create a normal PostInflow (cash refund)
    //             $data = new PostInflow();
    //             $data->inflow_status = 3; // claimed status
    //             $data->customer_id = $returnItem->customer_id;
    //             $data->amount = $returnAmount;
    //             $data->narration = "Return of items";
    //             $data->inflow_date = now();
    //             $data->save();

    //             $returnItem->post_inflow_id = $data->id;
    //             $returnItem->save();
    //         }
    //     }

    //     return new ReturnItemResource($returnItem);
    // }

    //     public function approve(Request $request)
    // {
    //     $validated = $request->validate([
    //         'comment' => ['nullable'],
    //         'status' => ['required', 'string'],
    //         'id' => ['required']
    //     ]);

    //     $returnItem = ReturnItem::findOrFail($validated['id']);
    //     $returnItem->approval_comment = $validated['comment'];
    //     $returnItem->return_status = $validated['status'];
    //     $returnItem->approved_by = auth()->user()->id;
    //     $returnItem->approved_at = now();
    //     $returnItem->save();

    //     if ($validated['status'] == 'Approved') {
    //         // Get the original sales order to check payment type
    //         $salesReceipt = $returnItem->salesReceipt;
    //         $salesOrder = $salesReceipt ? SalesOrder::find($salesReceipt->sales_order_id) : null;

    //         $returnAmount = $returnItem->returnDetails->sum(function ($detail) {
    //             return $detail->return_quantity * $detail->unit_price;
    //         });

    //         if ($salesOrder && $salesOrder->payment_type === 'Credit') {
    //             // For credit purchases, handle the return
    //             $customer = Customer::find($returnItem->customer_id);
    //             if ($customer) {
    //                 $previousBalance = $customer->credit_balance;
    //                 $creditPortion = min($returnAmount, $previousBalance);
    //                 $cashPortion = max(0, $returnAmount - $previousBalance);

    //                 // Handle credit portion
    //                 if ($creditPortion > 0) {
    //                     $customer->credit_balance = max(0, $previousBalance - $creditPortion);
    //                     $customer->save();

    //                     // Find the original credit transaction
    //                     $creditTransaction = CreditTransaction::where('sales_order_id', $salesOrder->id)
    //                         ->where('type', 'credit')
    //                         ->first();

    //                     if ($creditTransaction) {
    //                         // Update the existing credit transaction
    //                         $creditTransaction->update([
    //                             'amount' => $creditTransaction->amount - $creditPortion,
    //                             'credit_balance_after' => $customer->credit_balance,
    //                             'notes' => $creditTransaction->notes . "\nAdjusted by -{$creditPortion} for return #{$returnItem->id}"
    //                         ]);

    //                         // Link the return item to the credit transaction
    //                         $returnItem->credit_transaction_id = $creditTransaction->id;
    //                     } else {
    //                         // Fallback: Create new adjustment if original not found
    //                         $creditTransaction = new CreditTransaction();
    //                         $creditTransaction->customer_id = $returnItem->customer_id;
    //                         $creditTransaction->branch_id = $returnItem->branch_id;
    //                         $creditTransaction->sales_order_id = $salesOrder->id;
    //                         $creditTransaction->type = 'return_adjustment';
    //                         $creditTransaction->amount = $creditPortion;
    //                         $creditTransaction->credit_balance_before = $previousBalance;
    //                         $creditTransaction->credit_balance_after = $customer->credit_balance;
    //                         $creditTransaction->transaction_date = now();
    //                         $creditTransaction->created_by = auth()->user()->id;
    //                         $creditTransaction->notes = "Credit adjustment for return #{$returnItem->id}";
    //                         $creditTransaction->save();

    //                         $returnItem->credit_transaction_id = $creditTransaction->id;
    //                     }
    //                 }

    //                 // Handle cash portion (if any)
    //                 if ($cashPortion > 0) {
    //                     $data = new PostInflow();
    //                     $data->inflow_status = 3; // claimed status
    //                     $data->customer_id = $returnItem->customer_id;
    //                     $data->amount = $cashPortion;
    //                     $data->narration = "Cash refund from return #{$returnItem->id} (excess over credit)";
    //                     $data->inflow_date = now();
    //                     $data->save();

    //                     $returnItem->post_inflow_id = $data->id;
    //                 }

    //                 $returnItem->save();
    //             }
    //         } else {
    //             // For cash purchases, create a normal PostInflow (cash refund)
    //             $data = new PostInflow();
    //             $data->inflow_status = 3; // claimed status
    //             $data->customer_id = $returnItem->customer_id;
    //             $data->amount = $returnAmount;
    //             $data->narration = "Return of items";
    //             $data->inflow_date = now();
    //             $data->save();

    //             $returnItem->post_inflow_id = $data->id;
    //             $returnItem->save();
    //         }
    //     }

    //     return new ReturnItemResource($returnItem);
    // }

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
