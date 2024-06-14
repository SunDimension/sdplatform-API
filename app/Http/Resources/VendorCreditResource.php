<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorCreditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'warehouse_id' => $this->warehouse_id,
            'credit_number' => $this->credit_number,
            'purchase_order_number' => $this->purchase_order_number,
            'vendor_credit_date' => $this->vendor_credit_date,
        ];
    }
}
