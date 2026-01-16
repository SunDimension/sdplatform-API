<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditTransactionStoreRequest;
use App\Http\Requests\CreditTransactionUpdateRequest;
use App\Http\Resources\CreditTransactionCollection;
use App\Http\Resources\CreditTransactionResource;
use App\Http\Resources\SalesOrderCollection;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesReceipt;
use App\Models\ProductAudit;
use App\Models\StoreItem;
use App\Classes\StockUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditTransactionController extends Controller
{
    public function index(Request $request)
    {
        $creditTransactions = CreditTransaction::all();

        return new CreditTransactionCollection($creditTransactions);
    }


    public function pendingPayment()
    {
        $orders = SalesOrder::where('payment_type', 'Credit')
            ->whereIn('status', ['Approved', 'Paid'])
            ->where('branch_id', auth()->user()->branch_id)
            ->withSum('salesReceipts as total_paid', 'amount_paid')
            ->having('total_paid', '<', DB::raw('total_amount'))
            ->get();

        // Log::info("Da", $orders);
        return new SalesOrderCollection($orders);
    }


    public function store(CreditTransactionStoreRequest $request)
    {
        $data = $request->validated();
        $data["created_by"] = auth()->user()->id;

        // Calculate previous debt (not based on credit limit)
        $totalCredit = CreditTransaction::where('customer_id', $data['customer_id'])
            ->where('type', 'credit')
            ->sum('amount');
        $totalPayment = CreditTransaction::where('customer_id', $data['customer_id'])
            ->where('type', 'payment')
            ->sum('amount');
        $previousDebt = $totalCredit - $totalPayment;
        $data['credit_balance_before'] = $previousDebt;
        $data['credit_balance_after'] = $previousDebt + $data['amount'];

        $creditTransaction = CreditTransaction::create($data);
        if ($creditTransaction->type == 'credit') {
            $salesOrder = SalesOrder::findOrFail($creditTransaction->sales_order_id);
            // Remove credit_limit from calculation and assignment
            $salesOrder->credit_balance = $creditTransaction->amount;
            $salesOrder->status = "Pending";

            if ($salesOrder->total_amount == $creditTransaction->amount)
                $salesOrder->status = "Approved";
            $salesOrder->save();

            $customer = Customer::findOrFail($creditTransaction->customer_id);
            $customer->credit_balance = $data['credit_balance_after'];
            $customer->save();


            if ($salesOrder->status == "Approved") {
                $data = [
                    'sales_order_id' => $creditTransaction->sales_order_id,
                    'branch_id' => $creditTransaction->branch_id,
                    'payment_type' => 'Cash',
                    'credit_order_number' => $creditTransaction->credit_order_number,
                    'customer_id' => $creditTransaction->customer_id,
                    'store_id' => $salesOrder->store_id,
                    'user_id' => $creditTransaction->branch_id,
                    'cashier_id' => $creditTransaction->branch_id,
                    'total_amount' => $salesOrder->total_amount,
                    'amount_paid' => 0,
                    'receipt_date' => now(),
                    'payment_detail' => [["amount" => $salesOrder->total_amount, "payment_type" => "Credit"]],
                ];
                SalesReceipt::create($data);
            }
        } elseif ($creditTransaction->type == 'payment') {
            $creditTransaction->type == 'credit';
            $customer = Customer::findOrFail($creditTransaction->customer_id);
            $customer->credit_balance = $data['credit_balance_after'];
            //$customer->credit_balance + $creditTransaction->amount;
            $customer->save();
        }




        return new CreditTransactionResource($creditTransaction);
    }


    public function getForOrder($salesOrderId)
    {
        $transaction = CreditTransaction::where('sales_order_id', $salesOrderId)
            ->where('type', 'credit')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Credit transaction not found'], 404);
        }

        return new CreditTransactionResource($transaction);
    }

    public function returnAdjustments(Request $request, $customerId = null)
    {
        $query = CreditTransaction::where('type', 'return_adjustment')
            ->with(['customer', 'branch', 'salesOrder']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return new CreditTransactionCollection($query->get());
    }

    public function show(Request $request, CreditTransaction $creditTransaction)
    {
        return new CreditTransactionResource($creditTransaction);
    }

    public function update(CreditTransactionUpdateRequest $request, CreditTransaction $creditTransaction)
    {
        $creditTransaction->update($request->validated());

        return new CreditTransactionResource($creditTransaction);
    }

    public function destroy(Request $request, CreditTransaction $creditTransaction)
    {
        $creditTransaction->delete();

        return response()->noContent();
    }

    public function searchCredit(Request $request)
    {
        $validated = $request->validate([
            'credit_order_number' => 'required|string|exists:credit_transactions,credit_order_number'
        ]);

        try {
            $receipt = CreditTransaction::with([
                'salesOrder' => function ($query) {
                    $query->with(['customer', 'branch', 'store', 'itemSold' => function ($q) {
                        $q->with(['product', 'store']);
                    }]);
                }
            ])->where('credit_order_number', $validated['credit_order_number'])->firstOrFail();

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

    // public function cancelCredit($id)
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
}
