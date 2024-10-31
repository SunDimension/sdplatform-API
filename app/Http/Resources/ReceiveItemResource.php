<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receive_order_id' => $this->receive_order_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'product_id' => $this->product_id,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
