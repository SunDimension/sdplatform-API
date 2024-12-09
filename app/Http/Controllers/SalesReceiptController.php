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
use App\Models\SalesReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesReceiptController extends Controller
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
        $query = SalesReceipt::with(['customer', 'store', 'user', 'branch', 'salesorder'])
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
        $salesReceipts = $query->get();

        // Return as a resource collection
        return new SalesReceiptCollection($salesReceipts);
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

    public function pendingReleaseStore($storeId)
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        //$user = Auth::user();
        // Log::debug($user);

        $salesReceipts = SalesReceipt::with('salesOrder')->whereHas('salesOrder.itemsold', function ($query) use ($storeId) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $storeId)->where('status', 'pending'); // Example condition
        })->get();
        //Log::debug($salesOrders);
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

    public function store(SalesReceiptStoreRequest $request)
    {
        $data = $request->validated();
        $salesreceipt = SalesReceipt::create($data);
        $order = SalesOrder::where('id', $salesreceipt->sales_order_id)->first();
        $order->status = 'Paid';
        $order->save();

        $payments = $data['payment_detail'];

        $filteredPayments = array_filter($payments, function ($payment) {
            return $payment['payment_type']  == 'Deposit';
        });

        $filteredPayments = array_filter($payments, function ($payment) {
            return $payment['payment_type']  == 'Deposit';
        });

        $filteredPayments1 = array_filter($payments, function ($payment) {
            return $payment['payment_type']  == 'Credit';
        });

        if (count($filteredPayments) == 1) {
            $filteredPayments = array_values($filteredPayments);
            Log::debug($filteredPayments);
            $outflow = ["customer_id" => $salesreceipt->customer_id, "sales_receipt_id" => $salesreceipt->id, "amount" => $filteredPayments[0]['amount'], 'outflow_mode' => 7, 'outflow_date' => now()];
            PostOutflow::create($outflow);
        }

        if ($order->payment_type == "Credit" && count($filteredPayments1) == 0) {
            $customer = Customer::findOrFail($salesreceipt->customer_id);
            $data1 = [
                'branch_id' => $salesreceipt->branch_id,
                'customer_id' => $salesreceipt->customer_id,
                'sales_receipt_id' => $salesreceipt->id,
                'amount' => $salesreceipt->amount_paid,
                'credit_limit' => $customer->credit_limit,
                'credit_balance_before' => $customer->credit_balance,
                'type' => 'payment',
                'created_by' => auth()->user()->id
            ];
            $creditTransaction = CreditTransaction::create($data1);
            $customer->credit_balance = $customer->credit_balance + $creditTransaction->amount;
            $customer->save();
        }

        //return new SalesReceiptResource($salesreceipt);
        return response()->json(['message' => 'Sales Receipt Created Successfully', 'data' => $salesreceipt], 200);
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
}
