<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subscription_id' => $this->subscription_id,
            'property_id' => $this->property_id,
            'currency' => $this->currency,
            'gateway' => $this->gateway,
            'transaction_reference' => $this->transaction_reference,
            'paid_at' => $this->paid_at,
            'amount' => $this->amount,
            'payment_status' => $this->payment_status,
        ];
    }
}
