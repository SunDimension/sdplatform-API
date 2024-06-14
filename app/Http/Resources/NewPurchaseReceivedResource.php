<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewPurchaseReceivedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'purchase_order_number' => $this->purchase_order_number,
            'purchase_received_number' => $this->purchase_received_number,
            'received_date' => $this->received_date,
        ];
    }
}
