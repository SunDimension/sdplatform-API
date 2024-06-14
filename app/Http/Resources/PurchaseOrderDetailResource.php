<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_category_id' => $this->item_category_id,
            'purchase_order_id' => $this->purchase_order_id,
            'item_id' => $this->item_id,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
        ];
    }
}
