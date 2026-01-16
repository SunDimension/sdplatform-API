<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierInvoiceItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_item_id' => $this->invoice_item_id,
            'invoice_id' => $this->invoice_id,
            'product_id' => $this->product_id,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'unit_price_formatted' => number_format($this->unit_price, 2),
            'amount' => (float) $this->amount,
            'amount_formatted' => number_format($this->amount, 2),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Product information
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'product_id' => $this->product->product_id,
                    'name' => $this->product->product->name ?? 'Unknown Product',
                    'description' => $this->product->product->description ?? null,
                    'unit' => $this->product->product->unit ?? null,
                    'cost_price' => $this->product->new_cost_price ?? null,
                ];
            }),

            // Invoice information
            'invoice' => $this->whenLoaded('supplierInvoice', function () {
                return [
                    'invoice_id' => $this->supplierInvoice->invoice_id,
                    'invoice_number' => $this->supplierInvoice->invoice_number,
                    'invoice_date' => $this->supplierInvoice->invoice_date?->format('Y-m-d'),
                    'status' => $this->supplierInvoice->status,
                    'total_amount' => (float) $this->supplierInvoice->total_amount,
                ];
            }),
        ];
    }
}