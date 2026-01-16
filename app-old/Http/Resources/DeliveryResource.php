<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'sales_order_number' => $this->sales_order_number,
            'delivery_order_number' => $this->delivery_order_number,
            'delivery_date' => $this->delivery_date,
            'carrier_id' => $this->carrier_id,
            'notes' => $this->notes,
        ];
    }
}
