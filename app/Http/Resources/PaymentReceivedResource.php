<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReceivedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'amount_received' => $this->amount_received,
            'bank_charges' => $this->bank_charges,
            'payment_number' => $this->payment_number,
            'deposit_bank_id' => $this->deposit_bank_id,
            'payment_mode_id' => $this->payment_mode_id,
            'invoice_number' => $this->invoice_number,
        ];
    }
}
