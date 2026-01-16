<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_id' => $this->invoice_id,
            'invoice_number' => $this->invoice_number,
            'supplier_id' => $this->supplier_id,
            'gr_id' => $this->gr_id,
            'invoice_date' => $this->invoice_date?->format('Y-m-d'),
            'invoice_date_formatted' => $this->invoice_date?->format('M d, Y'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'due_date_formatted' => $this->due_date?->format('M d, Y'),
            'total_amount' => (float) $this->total_amount,
            'total_amount_formatted' => number_format($this->total_amount, 2),
            'amount_paid' => (float) ($this->amount_paid ?? 0),    // ← MUST INCLUDE
            'status' => $this->status,
            'payment_date' => $this->payment_date,
            'reference_no' => $this->reference_no,
            'payment_id' => $this->payment_id,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('M d, Y h:i A'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'approved_at_formatted' => $this->approved_at?->format('M d, Y h:i A'),

            // Supplier information
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'supplier_id' => $this->supplier->supplier_id,
                    'supplier_name' => $this->supplier->supplier_name,
                    'email' => $this->supplier->email,
                    'phone' => $this->supplier->phone,
                    'address' => $this->supplier->address,
                ];
            }),

            // Goods Received information
            'goods_received' => $this->whenLoaded('goodsReceived', function () {
                if (!$this->goodsReceived) return null;

                return [
                    'gr_id' => $this->goodsReceived->gr_id,
                    'received_date' => $this->goodsReceived->received_date?->format('Y-m-d'),
                    'received_date_formatted' => $this->goodsReceived->received_date?->format('M d, Y'),
                    'status' => $this->goodsReceived->status,
                ];
            }),

            // Purchase Order information (through goods received)
            'purchase_order' => $this->whenLoaded('goodsReceived.purchaseOrder', function () {
                if (!$this->goodsReceived || !$this->goodsReceived->purchaseOrder) return null;

                return [
                    'po_id' => $this->goodsReceived->purchaseOrder->po_id,
                    'po_number' => $this->goodsReceived->purchaseOrder->po_number,
                    'order_date' => $this->goodsReceived->purchaseOrder->order_date?->format('Y-m-d'),
                    'order_date_formatted' => $this->goodsReceived->purchaseOrder->order_date?->format('M d, Y'),
                    'status' => $this->goodsReceived->purchaseOrder->status,
                ];
            }),

            // Invoice items
            'items' => $this->whenLoaded('supplierInvoiceItems', function () {
                return $this->supplierInvoiceItems->map(function ($item) {
                    return [
                        'invoice_item_id' => $item->invoice_item_id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product?->product?->name ?? 'Unknown Product',
                        'quantity' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'unit_price_formatted' => number_format($item->unit_price, 2),
                        'amount' => (float) $item->amount,
                        'amount_formatted' => number_format($item->amount, 2),
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'product_id' => $item->product->product_id,
                            'product_name' => $item->product->product->name ?? 'Unknown',
                            'unit' => $item->product->product->unit ?? null,
                        ] : null,
                    ];
                });
            }),

            // User information
            'created_by_user' => $this->whenLoaded('createdByUser', function () {
                return $this->createdByUser ? [
                    'id' => $this->createdByUser->id,
                    'name' => $this->createdByUser->name,
                    'email' => $this->createdByUser->email,
                ] : null;
            }),

            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return $this->approvedByUser ? [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                    'email' => $this->approvedByUser->email,
                ] : null;
            }),

            // Computed fields
            'is_overdue' => $this->isOverdue(),
            'days_overdue' => $this->getDaysOverdue(),
            'payment_status_color' => $this->getPaymentStatusColor(),
        ];
    }

    /**
     * Get the status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Paid',
            'unpaid' => 'Unpaid',
            default => ucfirst($this->status)
        };
    }

    /**
     * Check if invoice is overdue
     */
    private function isOverdue(): bool
    {
        if ($this->status === 'paid') {
            return false;
        }

        if (!$this->due_date) {
            return false;
        }

        return now()->greaterThan($this->due_date);
    }

    /**
     * Get days overdue
     */
    private function getDaysOverdue(): ?int
    {
        if (!$this->isOverdue() || !$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Get payment status color for UI
     */
    private function getPaymentStatusColor(): string
    {
        if ($this->isOverdue()) {
            return 'error';
        }

        return match ($this->status) {
            'paid' => 'success',
            'unpaid' => 'warning',
            default => 'default'
        };
    }
}
