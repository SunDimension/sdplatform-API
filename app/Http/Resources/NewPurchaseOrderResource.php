<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewPurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_category_id' => $this->item_category_id,
            'item_id' => $this->item_id,
            'vendor_id' => $this->vendor_id,
            'branch_id' => $this->branch_id,
            'payment_mode_id' => $this->payment_mode_id,
            'purchase_order_number' => $this->purchase_order_number,
            'purchase_amount' => $this->purchase_amount,
            'purchase_date' => $this->purchase_date,
            'expected_delivery_date' => $this->expected_delivery_date,
            'payment_type_id' => $this->payment_type_id,
        ];
    }
}
