<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'product_id' => $this->product_id,
            'tax_id' => $this->tax_id,
            'payment_mode_id' => $this->payment_mode_id,
            'discount_id' => $this->discount_id,
            'quantity' => $this->quantity,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'receipt_date' => $this->receipt_date,
            'customer_note' => $this->customer_note,
        ];
    }
}
