<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentVoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'expense_date' => $this->expense_date,
            'amount' => $this->amount,
            'description' => $this->description,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'tax_id' => $this->tax_id,
            'vendor_id' => $this->vendor_id,
            'payment_mode_id' => $this->payment_mode_id,
            'expense_account_id' => $this->expense_account_id,
        ];
    }
}
