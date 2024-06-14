<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount_id' => $this->discount_id,
            'discount' => $this->discount,
            'sales_order_number' => $this->sales_order_number,
            'total_amount' => $this->total_amount,
            'amount_paid' => $this->amount_paid,
            'balance_amount' => $this->balance_amount,
            'payment_mode' => $this->payment_mode,
        ];
    }
}
