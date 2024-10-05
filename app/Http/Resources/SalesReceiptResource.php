<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'store_id' => $this->store_id,
            'sales_receipt_number'=>$this->sales_receipt_number,
            'payment_mode_id' => $this->payment_mode_id,
            'sales_order' => $this->sales_order,
            'sales_invoice' => $this->sales_invoice,
            'amount_paid' => $this->amount_paid,
            'receipt_date' => $this->receipt_date,
            'total_amount' => $this->total_amount,
        ];
    }
}
