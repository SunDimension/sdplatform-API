<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReceiptStoreRequest;
use App\Http\Requests\SalesReceiptUpdateRequest;
use App\Http\Resources\SalesReceiptCollection;
use App\Http\Resources\SalesReceiptResource;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\PostOutflow;
use App\Models\SalesOrder;
use App\Classes\StockUtil;
use App\Models\PostInflow;
use App\Models\ProductAudit;
use App\Models\SalesReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReceiptController extends Controller
{



  

    // public function index(Request $request)
    // {
    //     // Validate and retrieve query parameters from the request
    //     $validated = $request->validate([
    //         'store_id' => 'nullable|integer|exists:stores,id',
    //         'branch_id' => 'nullable|integer|exists:stores,branch_id',
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date',
    //         'with_returns' => 'nullable|boolean'
    //     ]);

    //     // Extract validated parameters
    //     $storeId = $validated['store_id'] ?? null;
    //     $branchId = $validated['branch_id'] ?? null;
    //     $fromDate = $validated['from_date'] ?? null;
    //     $toDate = $validated['to_date'] ?? null;
    //     $withReturns = $validated['with_returns'] ?? false;

    //     // Get the authenticated user
    //     $user = auth()->user();

    //     // Build the SalesReceipt query
    //     $receiptQuery = SalesReceipt::with([
    //         'customer',
    //         'store',
    //         'user',
    //         'branch',
    //         'salesorder',
    //         'returnItems' => function ($q) {
    //             $q->where('return_status', 'Approved')
    //                 ->with(['returnDetails.product']);
    //         }
    //     ])
    //         ->when($storeId, function ($query, $storeId) {
    //             return $query->where('store_id', $storeId);
    //         })
    //         ->when($branchId, function ($query, $branchId) {
    //             return $query->where('branch_id', $branchId);
    //         });

    //     // Handle date filtering
    //     if ($fromDate || $toDate) {
    //         $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    //         $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    //         if ($fromDate && $toDate) {
    //             $receiptQuery->whereBetween('created_at', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $receiptQuery->where('created_at', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $receiptQuery->where('created_at', '<=', $toDate);
    //         }

    //         $receiptQuery->where('branch_id', $user->branch_id);
    //     }

    //     // If with_returns is true, ensure return items are loaded
    //     if ($withReturns) {
    //         $receiptQuery->with([
    //             'returnItems' => function ($q) {
    //                 $q->where('return_status', 'Approved')
    //                     ->with(['returnDetails']);
    //             }
    //         ]);
    //     }

    //     // Fetch SalesReceipts
    //     $salesReceipts = $receiptQuery->get();

    //     // Calculate the gross total (original total before any adjustments)
    //     $grossTotalReceiptAmount = $salesReceipts->sum('total_amount');

    //     // Calculate total return amount and adjust each receipt's total_amount
    //     $totalReturnAmount = 0;
    //     $netSalesReceipts = $salesReceipts->map(function ($receipt) use (&$totalReturnAmount) {
    //         // Calculate return amount for this receipt
    //         $returnAmount = 0;
    //         if ($receipt->relationLoaded('returnItems') && $receipt->returnItems->isNotEmpty()) {
    //             foreach ($receipt->returnItems as $returnItem) {
    //                 if ($returnItem->return_status === 'Approved') {
    //                     if ($returnItem->relationLoaded('returnDetails') && $returnItem->returnDetails->isNotEmpty()) {
    //                         $returnAmount += $returnItem->returnDetails->sum(function ($detail) {
    //                             // Apply discount if available, otherwise use 0
    //                             $discount = $detail->discount ?? 0;
    //                             // Ensure discounted price is not negative
    //                             $discountedUnitPrice = max(0, ($detail->unit_price ?? 0) - $discount);
    //                             return ($detail->return_quantity ?? 0) * $discountedUnitPrice;
    //                         });
    //                     }
    //                 }
    //             }
    //         }
    //         $totalReturnAmount += $returnAmount;

    //         // Adjust total_amount to be net of returns
    //         $receipt->total_amount = $receipt->total_amount - $returnAmount;

    //         return $receipt;
    //     });

    //     // Calculate total sales receipt amount (sum of net amounts)
    //     $totalReceiptAmount = $netSalesReceipts->sum('total_amount');

    //     // Build the SalesOrder query based on the same filters
    //     $orderQuery = SalesOrder::query()
    //         ->when($storeId, function ($query, $storeId) {
    //             return $query->where('store_id', $storeId);
    //         })
    //         ->when($branchId, function ($query, $branchId) {
    //             return $query->where('branch_id', $branchId);
    //         });

    //     // Apply the same date filtering to SalesOrder
    //     if ($fromDate || $toDate) {
    //         if ($fromDate && $toDate) {
    //             $orderQuery->whereBetween('created_at', [$fromDate, $toDate]);
    //         } elseif ($fromDate) {
    //             $orderQuery->where('created_at', '>=', $fromDate);
    //         } elseif ($toDate) {
    //             $orderQuery->where('created_at', '<=', $toDate);
    //         }

    //         $orderQuery->where('branch_id', $user->branch_id);
    //     }

    //     // Fetch SalesOrders and calculate total amount
    //     $salesOrders = $orderQuery->get();
    //     $totalOrderAmount = $salesOrders->sum('total_amount');

    //     // Calculate the difference (net receipt amount - sales order amount)
    //     $difference = $totalReceiptAmount - $totalOrderAmount;

    //     // Calculate net total correctly (gross total - returns)
    //     $netTotal = $grossTotalReceiptAmount - $totalReturnAmount;

    //     // Return the response
    //     return response()->json([
    //         'sales_receipts' => new SalesReceiptCollection($netSalesReceipts),
    //         'total_sales_receipt_amount' => $grossTotalReceiptAmount,
    //         'total_return_amount' => $totalReturnAmount,
    //         'total_sales_order_amount' => $totalOrderAmount,
    //         'difference' => $difference,
    //         'net_total' => $netTotal,
    //     ]);
    // }

    public function index(Request $request)
{
    // Validate and retrieve query parameters from the request
    $validated = $request->validate([
        'store_id' => 'nullable|integer|exists:stores,id',
        'branch_id' => 'nullable|integer|exists:stores,branch_id',
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
        'with_returns' => 'nullable|boolean'
    ]);

    // Extract validated parameters
    $storeId = $validated['store_id'] ?? null;
    $branchId = $validated['branch_id'] ?? null;
    $fromDate = $validated['from_date'] ?? null;
    $toDate = $validated['to_date'] ?? null;
    $withReturns = $validated['with_returns'] ?? false;

    // Get the authenticated user
    $user = auth()->user();

    // Build the SalesReceipt query
    $receiptQuery = SalesReceipt::with([
        'customer',
        'store',
        'user',
        'branch',
        'salesorder',
        'returnItems' => function ($q) {
            $q->where('return_status', 'Approved')
                ->with(['returnDetails.product']);
        }
    ])
        ->when($storeId, function ($query, $storeId) {
            return $query->where('store_id', $storeId);
        })
        ->when($branchId, function ($query, $branchId) {
            return $query->where('branch_id', $branchId);
        });

    // Handle date filtering
    if ($fromDate || $toDate) {
        $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

        if ($fromDate && $toDate) {
            $receiptQuery->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $receiptQuery->where('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $receiptQuery->where('created_at', '<=', $toDate);
        }

        $receiptQuery->where('branch_id', $user->branch_id);
    }

    // If with_returns is true, ensure return items are loaded
    if ($withReturns) {
        $receiptQuery->with([
            'returnItems' => function ($q) {
                $q->where('return_status', 'Approved')
                    ->with(['returnDetails']);
            }
        ]);
    }

    // Fetch SalesReceipts
    $salesReceipts = $receiptQuery->get();

    // Calculate the gross total (original total before any adjustments)
    $grossTotalReceiptAmount = $salesReceipts->sum('total_amount');

    // Calculate total return amount and adjust each receipt's total_amount
    $totalReturnAmount = 0;
    $netSalesReceipts = $salesReceipts->map(function ($receipt) use (&$totalReturnAmount) {
        // Calculate return amount for this receipt
        $returnAmount = 0;
        if ($receipt->relationLoaded('returnItems') && $receipt->returnItems->isNotEmpty()) {
            foreach ($receipt->returnItems as $returnItem) {
                if ($returnItem->return_status === 'Approved') {
                    if ($returnItem->relationLoaded('returnDetails') && $returnItem->returnDetails->isNotEmpty()) {
                        $returnAmount += $returnItem->returnDetails->sum(function ($detail) {
                            // Apply discount if available, otherwise use 0
                            $discount = $detail->discount ?? 0;
                            // Ensure discounted price is not negative
                            $discountedUnitPrice = max(0, ($detail->unit_price ?? 0) - $discount);
                            return ($detail->return_quantity ?? 0) * $discountedUnitPrice;
                        });
                    }
                }
            }
        }
        $totalReturnAmount += $returnAmount;

        // Add calculated return amount as an attribute to the receipt
        $receipt->calculated_return_amount = $returnAmount;
        
        // Store original total amount before adjustment
        $receipt->original_total_amount = $receipt->total_amount;
        
        // Adjust total_amount to be net of returns
        $receipt->total_amount = $receipt->total_amount - $returnAmount;

        return $receipt;
    });

    // Calculate total sales receipt amount (sum of net amounts)
    $totalReceiptAmount = $netSalesReceipts->sum('total_amount');

    // Build the SalesOrder query based on the same filters
    $orderQuery = SalesOrder::query()
        ->when($storeId, function ($query, $storeId) {
            return $query->where('store_id', $storeId);
        })
        ->when($branchId, function ($query, $branchId) {
            return $query->where('branch_id', $branchId);
        });

    // Apply the same date filtering to SalesOrder
    if ($fromDate || $toDate) {
        if ($fromDate && $toDate) {
            $orderQuery->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $orderQuery->where('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $orderQuery->where('created_at', '<=', $toDate);
        }

        $orderQuery->where('branch_id', $user->branch_id);
    }

    // Fetch SalesOrders and calculate total amount
    $salesOrders = $orderQuery->get();
    $totalOrderAmount = $salesOrders->sum('total_amount');

    // Calculate the difference (net receipt amount - sales order amount)
    $difference = $totalReceiptAmount - $totalOrderAmount;

    // Calculate net total correctly (gross total - returns)
    $netTotal = $grossTotalReceiptAmount - $totalReturnAmount;

    // Return the response
    return response()->json([
        'sales_receipts' => new SalesReceiptCollection($netSalesReceipts),
        'total_sales_receipt_amount' => $grossTotalReceiptAmount,
        'total_return_amount' => $totalReturnAmount,
        'total_sales_order_amount' => $totalOrderAmount,
        'difference' => $difference,
        'net_total' => $netTotal,
    ]);
}





    public function myReceipts(Request $request)
    {

        $validated = $request->validate([
            // 'store_id' => 'nullable|integer|exists:stores,id',
            // 'branch_id' => 'nullable|integer|exists:stores,branch_id', // Ensure branch_id exists in stores
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);
        $user = auth()->user();
        // $storeId = $user->store_id;
        // $branchId = $user->branch_id;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        // Start building the query
        $query = SalesReceipt::with(['customer', 'store', 'user', 'branch', 'salesorder'])->where('user_id', $user->id);

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
        $salesReceipts = $query->get();

        // Return as a resource collection
        return new SalesReceiptCollection($salesReceipts);
    }

    public function getbynumber($orderno)
    {
        $salesOrders = SalesReceipt::with(['salesOrder', 'salesOrder.itemSold'])->where('sales_receipt_number', $orderno)->first();
        Log::debug($salesOrders);
        return response()->json(['data' => new SalesReceiptResource($salesOrders)]);
    }

    public function pendingRelease()
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        $user = Auth::user();
        // Log::debug($user);

        $salesReceipts = SalesReceipt::with('salesOrder')->whereHas('salesOrder.itemsold', function ($query) use ($user) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $user->store_id)->where('status', 'pending'); // Example condition
        })->get();
        //Log::debug($salesOrders);
        return response()->json(['data' => SalesReceiptResource::collection($salesReceipts)]);
    }




    public function pendingReleaseStore($storeId, Request $request)
    {
        // Get today's date
        $today = now()->format('Y-m-d');

        // Initialize the query
        $query = SalesReceipt::with('salesOrder')
            ->whereHas('salesOrder.itemsold', function ($query) use ($storeId) {
                $query->where('store_id', $storeId)->where('status', 'pending');
            });

        // Check if start_date and end_date are provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // If both dates are the same, filter for just that day
            if ($startDate === $endDate) {
                $query->whereDate('created_at', $startDate);
            } else {
                // Otherwise, apply date range
                $query->whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
            }
        } else {
            // Default to today's data if no date range is provided
            $query->whereDate('created_at', $today);
        }

        // Execute the query
        $salesReceipts = $query->get();

        return response()->json(['data' => SalesReceiptResource::collection($salesReceipts)]);
    }





    public function pendingReleaseOrder($orderno)
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        $user = Auth::user();
        //Log::debug($user);

        $salesReceipts = SalesReceipt::where("sales_receipt_number", $orderno)->whereHas('salesOrder.itemSold', function ($query) use ($user) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $user->store_id); // Example condition
        })->with(['salesOrder', 'salesOrder.itemSold' => function ($query) use ($user) {
            // Only retrieve specific fields from ItemSold
            $query->where('store_id', $user->store_id)->where('status', 'pending');
        }])
            // ->select('id', 'sales_order_id', 'receipt_number') // Select specific fields from SalesReceipt
            ->first();
        //Log::debug($salesReceipts);
        return response()->json(['data' => new SalesReceiptResource($salesReceipts)]);
    }

    public function pendingReleaseOrder2($orderno, $storeId)
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        $user = Auth::user();
        //Log::debug($user);

        $salesReceipts = SalesReceipt::where("sales_receipt_number", $orderno)->whereHas('salesOrder.itemSold', function ($query) use ($storeId) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $storeId); // Example condition
        })->with(['salesOrder', 'salesOrder.itemSold' => function ($query) use ($storeId) {
            // Only retrieve specific fields from ItemSold
            $query->where('store_id', $storeId)->where('status', 'pending');
        }])
            // ->select('id', 'sales_order_id', 'receipt_number') // Select specific fields from SalesReceipt
            ->first();
        //Log::debug($salesReceipts);
        return response()->json(['data' => new SalesReceiptResource($salesReceipts)]);
    }

    // In your SalesReceiptController's store method
    public function store(SalesReceiptStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $salesreceipt = SalesReceipt::create($data);

            // Update the sales order status
            $order = SalesOrder::where('id', $salesreceipt->sales_order_id)->first();
            $order->status = 'Paid';
            $order->save();





            $payments = $data['payment_detail'];
            $customerId = $salesreceipt->customer_id;

            // Process deposit payments
            $depositPayments = array_filter($payments, function ($payment) {
                return $payment['payment_type'] == 'Deposit';
            });

            foreach ($depositPayments as $payment) {
                $amountUsed = $payment['amount'];

                // Get available deposits (FIFO order)
                $postInflows = PostInflow::where('customer_id', $customerId)
                    ->where('inflow_status', 3) // Assigned status
                    ->where('amount', '>', 0)
                    ->orderBy('inflow_date', 'asc')
                    ->lockForUpdate() // Prevent concurrent updates
                    ->get();

                foreach ($postInflows as $inflow) {
                    if ($amountUsed <= 0) break;

                    $available = $inflow->amount;
                    $used = min($available, $amountUsed);

                    // Update the inflow record
                    $inflow->amount -= $used;
                    $inflow->amount_used += $used;

                    // Update status if fully used
                    if ($inflow->amount <= 0) {
                        $inflow->inflow_status = 12; // Fully Used status
                    }

                    $inflow->save();
                    $amountUsed -= $used;
                }

                if ($amountUsed > 0) {
                    throw new \Exception("Customer $customerId used more deposit ($amountUsed) than available");
                }

                // Create outflow record
                $outflow = [
                    "customer_id" => $customerId,
                    "sales_receipt_id" => $salesreceipt->id,
                    "amount" => $payment['amount'],
                    'outflow_mode' => 7,
                    'outflow_date' => now()
                ];
                PostOutflow::create($outflow);
            }

            // Process credit payments (existing code)
            if (
                $order->payment_type == "Credit" &&
                !array_filter($payments, fn($p) => $p['payment_type'] == 'Credit')
            ) {
                $customer = Customer::findOrFail($customerId);
                $data1 = [
                    'branch_id' => $salesreceipt->branch_id,
                    'customer_id' => $customerId,
                    'sales_receipt_id' => $salesreceipt->id,
                    'amount' => $salesreceipt->amount_paid,
                    'credit_limit' => $customer->credit_limit,
                    'credit_balance_before' => $customer->credit_balance,
                    'type' => 'payment',
                    'created_by' => auth()->user()->id
                ];
                $creditTransaction = CreditTransaction::create($data1);
                $customer->credit_balance -= $creditTransaction->amount;
                $customer->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Sales Receipt Created Successfully',
                'data' => $salesreceipt
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Sales receipt creation failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create sales receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, SalesReceipt $salesreceipt): SalesReceiptResource
    {
        return new SalesReceiptResource($salesreceipt);
    }

    public function update(SalesReceiptUpdateRequest $request, SalesReceipt $salesreceipt): SalesReceiptResource
    {
        $salesreceipt->update($request->validated());

        return new SalesReceiptResource($salesreceipt);
    }

    public function destroy($id)
    {
        SalesReceipt::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function CustomerAndDate(Request $request)
    {
        // Validate and retrieve query parameters from the request
        $validated = $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id', // Ensure customer_id exists in customers table
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        // Extract validated parameters
        $customerId = $validated['customer_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        // Get the authenticated user
        $user = auth()->user();

        // Start building the query
        $query = SalesReceipt::with(['customer', 'store', 'user', 'branch', 'salesorder'])
            ->when($customerId, function ($query, $customerId) {
                return $query->where('customer_id', $customerId);
            });

        // Handle date filtering
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

        // Optionally, filter by the user's branch if needed
        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        // Fetch the results
        $salesReceipts = $query->get();

        // Return the results as a collection
        return new SalesReceiptCollection($salesReceipts);
    }

    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            // Load the sales receipt with all necessary relationships
            $salesReceipt = SalesReceipt::with([
                'salesOrder',
                'customer',
                'postOutflows',
                'creditTransactions'
            ])->findOrFail($id);

            // Check if already canceled
            if ($salesReceipt->status === 'Canceled') {
                return response()->json(['message' => 'Receipt is already canceled'], 400);
            }

            // 1. Reverse any deposit payments
            foreach ($salesReceipt->postOutflows as $outflow) {
                if ($outflow->outflow_mode === 7) { // Deposit payment type
                    // Create inflow record for the refund
                    PostInflow::create([
                        'customer_id' => $salesReceipt->customer_id,
                        'sales_receipt_id' => $salesReceipt->id,
                        'amount' => $outflow->amount,
                        'inflow_mode' => 8, // Deposit refund
                        'inflow_status' => 3, // Assigned
                        'inflow_date' => now(),
                        'notes' => 'Deposit refund from receipt cancellation'
                    ]);

                    // Mark the outflow as reversed
                    $outflow->update(['is_reversed' => true]);
                }
            }

            // 2. Reverse credit transactions if any
            foreach ($salesReceipt->creditTransactions as $creditTransaction) {
                if ($creditTransaction->type === 'payment') {
                    $customer = $salesReceipt->customer;
                    $previousBalance = $customer->credit_balance;

                    // Reduce the customer's credit balance
                    $customer->credit_balance -= $creditTransaction->amount;
                    $customer->save();

                    // Create a reversal transaction
                    CreditTransaction::create([
                        'branch_id' => $salesReceipt->branch_id,
                        'customer_id' => $customer->id,
                        'sales_receipt_id' => $salesReceipt->id,
                        'amount' => $creditTransaction->amount,
                        'credit_limit' => $customer->credit_limit,
                        'credit_balance_before' => $previousBalance,
                        'credit_balance_after' => $customer->credit_balance,
                        'type' => 'payment_reversal',
                        'created_by' => auth()->id(),
                        'notes' => 'Payment reversal from receipt cancellation'
                    ]);

                    // Mark original transaction as reversed
                    $creditTransaction->update(['is_reversed' => true]);
                }
            }

            // 3. Update the sales order status
            if ($salesReceipt->salesOrder) {
                $salesOrder = $salesReceipt->salesOrder;

                // Determine new status based on payment type
                $newStatus = ($salesOrder->payment_type === 'Credit')
                    ? 'Credit Pending'
                    : 'Pending';

                $salesOrder->update(['status' => $newStatus]);
            }

            // 4. Mark the receipt as canceled
            $salesReceipt->update([
                'status' => 'Canceled',
                'canceled_by' => auth()->id(),
                'canceled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Sales receipt canceled successfully',
                'data' => new SalesReceiptResource($salesReceipt)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to cancel sales receipt: " . $e->getMessage());

            return response()->json([
                'message' => 'Failed to cancel sales receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function blockReceipt($id)
    {
        $receipt = SalesReceipt::findOrFail($id);
        $receipt->blocked = true;
        $receipt->save();

        return response()->json(['message' => 'Receipt blocked from release.']);
    }

    public function unblockReceipt($id)
    {
        $receipt = SalesReceipt::findOrFail($id);
        $receipt->blocked = false;
        $receipt->save();

        return response()->json(['message' => 'Receipt unblocked for release.']);
    }

    public function searchForBlocking(Request $request)
    {
        $validated = $request->validate([
            'sales_receipt_number' => 'required|string'
        ]);

        $receiptNumber = trim($validated['sales_receipt_number']);

        try {
            $receipt = SalesReceipt::with([
                'salesOrder' => function ($query) {
                    $query->with(['customer', 'branch', 'store', 'itemSold' => function ($q) {
                        $q->with(['product', 'store']);
                    }]);
                }
            ])->where('sales_receipt_number', $receiptNumber)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $receipt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        }
    }
}
