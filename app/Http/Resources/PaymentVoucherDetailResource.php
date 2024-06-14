<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentVoucherDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'Expense_account_id' => $this->Expense_account_id,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'item_id' => $this->item_id,
        ];
    }
}
