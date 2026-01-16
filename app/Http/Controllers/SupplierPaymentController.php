<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierPaymentStoreRequest;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SupplierPaymentController extends Controller
{
    /**
     * Store a newly created supplier payment
     */
    // public function store(SupplierPaymentStoreRequest $request)
    // {
    //     // Start database transaction
    //     return DB::transaction(function () use ($request) {
    //         // Validate invoice exists and is unpaid
    //         $invoice = SupplierInvoice::where('invoice_id', $request->invoice_id)
    //             ->where('status', 'unpaid')
    //             ->first();

    //         if (!$invoice) {
    //             return response()->json([
    //                 'message' => 'Invoice not found or already paid'
    //             ], 404);
    //         }

    //         // Check if amount paid is valid (should not exceed invoice total)
    //         if ($request->amount_paid > $invoice->total_amount) {
    //             return response()->json([
    //                 'message' => 'Amount paid cannot exceed invoice total amount'
    //             ], 422);
    //         }

    //         // Check if invoice is partially paid
    //         $currentPaidAmount = $invoice->amount_paid ?? 0;
    //         $newPaidAmount = $currentPaidAmount + $request->amount_paid;

    //         // Determine new status based on payment amount
    //         if ($newPaidAmount >= $invoice->total_amount) {
    //             $newStatus = 'paid';
    //         } else {
    //             $newStatus = 'partial';
    //         }

    //         // Generate payment ID
    //         $paymentId = (string) Str::uuid();

    //         // Create the payment
    //         $payment = SupplierPayment::create([
    //             'payment_id' => $paymentId,
    //             'supplier_id' => $request->supplier_id,
    //             'invoice_id' => $request->invoice_id,
    //             'payment_date' => $request->payment_date,
    //             'payment_method' => $request->payment_method,
    //             'amount_paid' => $request->amount_paid,
    //             'reference_no' => $request->reference_no,
    //             'created_by' => auth()->id(),
    //             'sync_id' => Str::uuid(),
    //             'status' => 'completed',
    //         ]);

    //         // Update invoice status and payment details
    //         $invoice->update([
    //             'status' => $newStatus,
    //             'payment_id' => $paymentId,
    //             'payment_date' => $request->payment_date,
    //             'amount_paid' => $newPaidAmount,
    //             'reference_no' => $request->reference_no,
    //         ]);

    //         // Load relationships for response
    //         $payment->load([
    //             'invoice.supplier',
    //             'invoice.goodsReceived.purchaseOrder',
    //             'supplier',
    //             'createdByUser'
    //         ]);

    //         return response()->json([
    //             'message' => 'Supplier payment created successfully',
    //             'data' => [
    //                 'payment' => $payment,
    //                 'invoice' => $invoice->fresh() // Get fresh instance with updated data
    //             ]
    //         ], 201);
    //     }, 5);
    // }

    public function store(SupplierPaymentStoreRequest $request)
    {
        // Start database transaction
        return DB::transaction(function () use ($request) {
            // Validate invoice exists (it can be unpaid or partially paid)
            $invoice = SupplierInvoice::where('invoice_id', $request->invoice_id)->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Check if amount paid is valid (should not exceed invoice total)
            $currentPaidAmount = (float) ($invoice->amount_paid ?? 0);
            $newPaymentAmount = (float) $request->amount_paid;
            $totalAmount = (float) $invoice->total_amount;

            if ($currentPaidAmount + $newPaymentAmount > $totalAmount) {
                return response()->json([
                    'message' => 'Total payments cannot exceed invoice total amount'
                ], 422);
            }

            // Determine new status based on payment amount
            $newPaidAmount = $currentPaidAmount + $newPaymentAmount;

            // FIX: Use proper elseif syntax
            if ($newPaidAmount >= $totalAmount) {
                $newStatus = 'paid';
            } elseif ($newPaidAmount > 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'unpaid';
            }

            // Generate payment ID
            $paymentId = (string) Str::uuid();

            // Create the payment
            $payment = SupplierPayment::create([
                'payment_id' => $paymentId,
                'supplier_id' => $request->supplier_id,
                'invoice_id' => $request->invoice_id,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'amount_paid' => $newPaymentAmount,
                'reference_no' => $request->reference_no,
                'remarks' => $request->remarks ?? null,
                'created_by' => auth()->id(),
                'sync_id' => Str::uuid(),
                'status' => 'completed',
            ]);

            // Update invoice status and payment details
            $invoice->update([
                'status' => $newStatus,
                'payment_id' => $paymentId,
                'payment_date' => $request->payment_date,
                'amount_paid' => $newPaidAmount,
                'reference_no' => $request->reference_no,
            ]);

            // Load relationships for response
            $payment->load([
                'invoice.supplier',
                'invoice.goodsReceived.purchaseOrder',
                'supplier',
                'createdByUser'
            ]);

            return response()->json([
                'message' => 'Supplier payment created successfully',
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh() // Get fresh instance with updated data
                ]
            ], 201);
        }, 5);
    }

    /**
     * Get all payments with relationships
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|string|exists:suppliers,supplier_id',
            'invoice_id' => 'nullable|string|exists:supplier_invoices,invoice_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'payment_method' => 'nullable|string|in:Cash,Bank,Mobile Money',
        ]);

        $supplierId = $validated['supplier_id'] ?? null;
        $invoiceId = $validated['invoice_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;

        // Start building the query with eager loading
        $query = SupplierPayment::with([
            'supplier',
            'invoice',
            'invoice.goodsReceived.purchaseOrder',
            'createdByUser',
        ])
            ->when($supplierId, function ($query, $supplierId) {
                return $query->where('supplier_id', $supplierId);
            })
            ->when($invoiceId, function ($query, $invoiceId) {
                return $query->where('invoice_id', $invoiceId);
            })
            ->when($paymentMethod, function ($query, $paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            });

        // Handle date filtering for payment_date
        if ($fromDate && $toDate) {
            $query->whereBetween('payment_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('payment_date', '>=', Carbon::parse($fromDate)->startOfDay());
        } elseif ($toDate) {
            $query->where('payment_date', '<=', Carbon::parse($toDate)->endOfDay());
        }

        // Order by recent payments first
        $query->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Execute the query and get the results
        $supplierPayments = $query->get();

        return response()->json([
            'data' => $supplierPayments,
            'count' => $supplierPayments->count(),
            'total_amount' => $supplierPayments->sum('amount_paid')
        ]);
    }

    /**
     * Get specific payment details
     */
    public function show($id)
    {
        $payment = SupplierPayment::with([
            'supplier',
            'invoice',
            'invoice.goodsReceived.purchaseOrder',
            'invoice.supplierInvoiceItems.product.product',
            'createdByUser'
        ])->findOrFail($id);

        return response()->json([
            'data' => $payment
        ]);
    }

    /**
     * Get unpaid invoices for a specific supplier
     */
    public function getUnpaidInvoices(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|string|exists:suppliers,supplier_id',
        ]);

        $invoices = SupplierInvoice::with(['supplier'])
            ->where('supplier_id', $validated['supplier_id'])
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->get([
                'invoice_id',
                'invoice_number',
                'supplier_id',
                'invoice_date',
                'due_date',
                'total_amount',
                'amount_paid',
                'status'
            ]);

        return response()->json([
            'data' => $invoices,
            'total_unpaid' => $invoices->sum('total_amount')
        ]);
    }

    /**
     * Get payment summary by supplier
     */
    public function getPaymentSummary(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        $query = SupplierPayment::with(['supplier'])
            ->select('supplier_id')
            ->selectRaw('COUNT(*) as payment_count')
            ->selectRaw('SUM(amount_paid) as total_paid')
            ->groupBy('supplier_id');

        // Handle date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('payment_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        }

        $summary = $query->get();

        return response()->json([
            'data' => $summary
        ]);
    }
}
