<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierInvoiceStoreRequest;
use App\Http\Resources\SupplierInvoiceCollection;
use App\Http\Requests\GenerateInvoiceFromGRRequest;
use App\Http\Resources\SupplierInvoiceResource;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\GoodsRecieved;
use App\Models\GoodsRecievedItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SupplierInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|string|exists:suppliers,supplier_id',
            'gr_id' => 'nullable|string|exists:goode_recieveds,gr_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'nullable|string|in:paid,unpaid',
        ]);

        $supplierId = $validated['supplier_id'] ?? null;
        $grId = $validated['gr_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $status = $validated['status'] ?? null;

        // Start building the query with eager loading
        $query = SupplierInvoice::with([
            'supplier',
            'goodsReceived',
            'goodsReceived.purchaseOrder',
            'goodsReceived.purchaseOrder.supplier',
            'supplierInvoiceItems.product.product',
            'createdByUser',
            // 'approvedByUser',
        ])
            ->when($supplierId, function ($query, $supplierId) {
                return $query->where('supplier_id', $supplierId);
            })
            ->when($grId, function ($query, $grId) {
                return $query->where('gr_id', $grId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            });

        // Handle date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('invoice_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('invoice_date', '>=', Carbon::parse($fromDate)->startOfDay());
        } elseif ($toDate) {
            $query->where('invoice_date', '<=', Carbon::parse($toDate)->endOfDay());
        }

        // Order by recent first
        $query->orderBy('invoice_date', 'desc');

        // Execute the query and get the filtered results
        $supplierInvoices = $query->get();

        // Return as a resource collection
        return new SupplierInvoiceCollection($supplierInvoices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,supplier_id',
            'gr_id'         => 'required|exists:goods_recieveds,gr_id',
            'invoice_date'  => 'required|date',
            'due_date'      => 'required|date|after_or_equal:invoice_date',
            'total_amount'  => 'required|numeric|min:0',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity'   => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            /** 1️⃣ Create invoice header */
            $invoice = SupplierInvoice::create([
                'supplier_id'  => $request->supplier_id,
                'gr_id'        => $request->gr_id,
                'invoice_date' => $request->invoice_date,
                'due_date'     => $request->due_date,
                'total_amount' => $request->total_amount,
                // 'remarks'      => $request->remarks,
                'status'       => 'unpaid',
                'created_by'   => auth()->id(),
            ]);

            /** 2️⃣ Create invoice items */
            foreach ($request->items as $item) {
                SupplierInvoiceItem::create([
                    'invoice_id' => $invoice->invoice_id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount'     => $item['amount'],
                ]);
            }
            $goodsReceived = GoodsRecieved::where('gr_id', $request->gr_id)->first();

            if ($goodsReceived) {
                $goodsReceived->update([
                    'invoice_status' => 'invoiced'
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice generated successfully',
                'data' => new SupplierInvoiceResource($invoice->load('supplier', 'supplierInvoiceItems'))
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Invoice creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(SupplierInvoice $supplierInvoice): SupplierInvoiceResource
    {
        return new SupplierInvoiceResource(
            $supplierInvoice->load([
                'supplier',
                'goodsReceived',
                'goodsReceived.purchaseOrder',
                'goodsReceived.purchaseOrder.supplier',
                'goodsReceived.goodsReceivedItems.product',
                'supplierInvoiceItems.product',
                'createdByUser',
                'approvedByUser',
                // 'status'
            ])
        );
    }

    public function update(Request $request, SupplierInvoice $supplierInvoice)
    {
        // Only allow updating certain fields (not items once created)
        $validated = $request->validate([
            'status' => 'nullable|string|in:paid,unpaid',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'approved_by' => 'nullable|string|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // If updating status to 'paid', set approved info
            if (isset($validated['status']) && $validated['status'] === 'paid') {
                $validated['approved_by'] = auth()->id();
                $validated['approved_at'] = now();
            }

            // Update supplier invoice
            $supplierInvoice->update($validated);

            DB::commit();

            return new SupplierInvoiceResource(
                $supplierInvoice->load([
                    'supplier',
                    'goodsReceived',
                    'goodsReceived.purchaseOrder',
                    'goodsReceived.purchaseOrder.supplier',
                    'createdByUser',
                    'approvedByUser',
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supplier Invoice update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update supplier invoice',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $supplierInvoice = SupplierInvoice::findOrFail($id);

            // Check if invoice can be deleted (only if unpaid)
            if ($supplierInvoice->status === 'paid') {
                throw new \Exception('Cannot delete a paid invoice.');
            }

            // Delete associated invoice items
            SupplierInvoiceItem::where('invoice_id', $supplierInvoice->invoice_id)->delete();

            // Delete the supplier invoice record
            $supplierInvoice->delete();

            DB::commit();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supplier Invoice deletion failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete supplier invoice',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function invoicesSearch(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|string|exists:suppliers,supplier_id',
            'gr_id' => 'nullable|string|exists:goode_recieveds,gr_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'nullable|string|in:paid,partial,unpaid', // ← Add 'partial' here too!
        ]);

        $supplierId = $validated['supplier_id'] ?? null;
        $grId = $validated['gr_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $status = $validated['status'] ?? null;

        $query = SupplierInvoice::with([
            'supplier',
            'goodsReceived',
            'goodsReceived.purchaseOrder',
            'goodsReceived.purchaseOrder.supplier',
            'supplierInvoiceItems.product.product',
            'createdByUser',
        ])
            ->select([
                'invoice_id',
                'invoice_number',
                'supplier_id',
                'gr_id',
                'invoice_date',
                'due_date',
                'total_amount',
                'amount_paid',     // ← CRITICAL: Include this
                'status',          // ← CRITICAL: Include this
                'payment_id',
                'payment_date',
                'reference_no',
                'created_at',
                'updated_at',
            ])
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($grId, fn($q) => $q->where('gr_id', $grId))
            ->when($status, fn($q) => $q->where('status', $status)); // Now supports 'partial'

        // Date filtering
        if ($fromDate && $toDate) {
            $query->whereBetween('invoice_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        } elseif ($fromDate) {
            $query->where('invoice_date', '>=', Carbon::parse($fromDate));
        } elseif ($toDate) {
            $query->where('invoice_date', '<=', Carbon::parse($toDate));
        }

        $query->orderBy('invoice_date', 'desc');

        $supplierInvoices = $query->get();

        return new SupplierInvoiceCollection($supplierInvoices);
    }
}
