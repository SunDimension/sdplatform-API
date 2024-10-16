<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesOrderResource;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\ItemSold;
use App\Models\SalesInvoice;
use App\Models\SalesReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderController extends Controller
{

     public function index(Request $request)
    {
        // Optionally, you can add filtering and sorting capabilities here
        $salesOrders = SalesOrder::with('itemsold', 'salesInvoices', 'salesReceipts')
            ->paginate(10); // Paginate results, you can change the number as needed

        return response()->json($salesOrders);
    }

    public function pendingReceipts(Request $request)
    {
        // Optionally, you can add filtering and sorting capabilities here
        $salesOrders = SalesOrder::where('status','Pending')->whereNot('payment_type','Credit')->get(); 
        Log::debug($salesOrders);
        return response()->json(['data'=> SalesOrderResource::collection($salesOrders)]);
    }

    public function pendingCredit(Request $request)
    {
        // Optionally, you can add filtering and sorting capabilities here
        $salesOrders = SalesOrder::where('status','Pending')->where('payment_type','Credit')->get();
        return response()->json(['data'=> SalesOrderResource::collection($salesOrders)]);
    }

    public function getbynumber($orderno)
    {
        $salesOrders = SalesOrder::where('sales_order_number', $orderno)->first();
        return response()->json(['data'=>new SalesOrderResource($salesOrders)]);
    }

   // Method to create a new Sales Order
    public function store(Request $request)
    {
        // Validate incoming request data
       $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'store_id' => 'required|exists:stores,id',
            'credit_limit' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:create_items,id',
            'items.*.quantity' => 'required|integer',
            'items.*.unit_price' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'invoice' => 'nullable|array',
            'payment' => 'nullable|array',
            'payment.total_amount' => 'required|numeric',
            'payment.amount_paid' => 'required|numeric',
            'payment.payment_type' => 'required|string|in:Cash,Bank,Paylater,Credit', // Payment type now explicitly validated
            // 'payment.item_sold_id' => 'required|integer|exists:item_solds,id',  // item_sold_id is foreign key in sales_receipt
        ]);
        $salesOrderNumber = 'HGV-SO-' . strtoupper(uniqid());
        // Create a new Sales Order
        $salesOrder = SalesOrder::create([
            'sales_order_number' => $salesOrderNumber,
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'store_id' => $validated['store_id'],
            'credit_limit' => $validated['credit_limit'] ?? null,
            'total_amount' =>$validated['total_amount'] ?? null,
            'payment_type'=> $validated['payment']['payment_type'] 
        ]);

        Log::alert($validated);
        // Update Items Sold
        $itemSoldIds = [];
        foreach ($validated['items'] as $item) {
             $itemSold = ItemSold::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
                'store_id' => $validated['store_id'],
                'sales_date' => now(),
                 
            ]);
            $itemSoldIds[] = $itemSold->id;
        }
/*
        // Handle Sales Invoice if payment is deferred
        if (!empty($validated['invoice'])) {
            SalesInvoice::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $validated['invoice']['product_id'],
                'unit_price' => $validated['invoice']['unit_price'],
                'amount' => $validated['invoice']['amount'],
                'sales_invoice_number' => 'HGV-INV-' . strtoupper(uniqid()), // Unique auto-generated invoice number
                'invoice_date' => now(),
            ]);
        }

        // Handle Sales Receipt if payment is made
         DB::beginTransaction();
    try {
        // Create the SalesReceipt
        $salesReceiptData = [
           'sales_order_id' => $salesOrder->id,
            'customer_id' => $request->customer_id,
            'branch_id' => $request->branch_id,
            'store_id' => $request->store_id,
             // Get item_sold_id from the payment details
            'amount_paid' => $request->payment['amount_paid'],
            'total_amount' => $request->payment['total_amount'],
            'payment_type' => $request->payment['payment_type'],
            // Add any other necessary fields here
        ];

        // Create the SalesReceipt
        $salesReceipt = SalesReceipt::create($salesReceiptData);

        // If needed, link the receipt with items sold
        // This part will depend on your business logic
        // Example: Associate receipt with specific items
        // ItemSold::whereIn('id', $itemIds)->update(['sales_receipt_id' => $salesReceipt->id]);

        // Commit the transaction
        DB::commit();

        return response()->json($salesReceipt, 201);
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        // Handle exceptions and return a detailed error message
        return response()->json(['error' => $e->getMessage()], 500);
    }*/
        return response()->json(['message' => 'Sales Order Created Successfully', 'data' => $salesOrder], 200);
    }
    //   public function show(Request $request, SalesOrder $salesOrder): SalesOrderResource
    // {
    //     return new SalesOrderResource($salesOrder);
    // }
    // Method to fetch the Sales Order for editing
    public function edit($id)
    {
        $salesOrder = SalesOrder::with('itemsSold', 'salesInvoices', 'salesReceipts')->findOrFail($id);

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
            'invoice' => 'nullable|array',
            'payment' => 'nullable|array',
        ]);

        // Find and update the Sales Order
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->update([
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'store_id' => $validated['store_id'],
            'credit_limit' => $validated['credit_limit'] ?? null,
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
        if (!empty($validated['invoice'])) {
            SalesInvoice::updateOrCreate(
                [
                    'sales_order_id' => $salesOrder->id,
                    'sales_invoice_number' => $validated['invoice']['sales_invoice_number'], // Ensuring uniqueness
                ],
                [
                    'product_id' => $validated['invoice']['product_id'],
                    'unit_price' => $validated['invoice']['unit_price'],
                    'amount' => $validated['invoice']['amount'],
                    'invoice_date' => now(),
                ]
            );
        }

        // Update Sales Receipt if payment is made
        if (!empty($validated['payment'])) {
            SalesReceipt::updateOrCreate(
                [
                    'sales_order_id' => $salesOrder->id,
                    'sales_receipt_number' => $validated['payment']['sales_receipt_number'], // Ensuring uniqueness
                ],
                [
                    'sales_invoice_id' => $validated['payment']['sales_invoice_id'],
                    'customer_id' => $validated['customer_id'],
                    'branch_id' => $validated['branch_id'],
                    'store_id' => $validated['store_id'],
                    'total_amount' => $validated['payment']['total_amount'],
                    'amount_paid' => $validated['payment']['amount_paid'],
                    'payment_mode_id' => $validated['payment']['payment_mode_id'],
                    'receipt_date' => now(),
                ]
            );
        }

        return response()->json(['message' => 'Sales Order Updated Successfully', 'sales_order' => $salesOrder], 200);
    }
}
