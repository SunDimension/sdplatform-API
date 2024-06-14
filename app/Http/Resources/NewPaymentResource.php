<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'payment_amount' => $this->payment_amount,
            'payment_mode_id' => $this->payment_mode_id,
            'description' => $this->description,
        ];
    }
}
