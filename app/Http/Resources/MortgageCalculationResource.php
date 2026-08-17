<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MortgageCalculationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'property_id' => $this->property_id,
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'loan_term' => $this->loan_term,
            'monthly_payment' => $this->monthly_payment,
            'total_payment' => $this->total_payment,
        ];
    }
}
