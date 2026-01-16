<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transfer_order_number' => $this->transfer_order_number,
            'transfer_date' => $this->transfer_date,
            'transfer_reason' => $this->transfer_reason,
            'source_id' => $this->source_id,
            'destination_id' => $this->destination_id,
            'image_url' => $this->image_url,
            'transfer_quantity' => $this->transfer_quantity,
            'item_id' => $this->item_id,
        ];
    }
}
