<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'customer_id' => $this->customer_id,
            'invoice_number' => $this->invoice_number,
            'order_number' => $this->order_number,
            'invoice_date' => $this->invoice_date,
            'item_id' => $this->item_id,
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'discount_id' => $this->discount_id,
            'tax_id' => $this->tax_id,
            'amount' => $this->amount,
        ];
    }
}
