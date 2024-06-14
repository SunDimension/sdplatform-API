<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryAdjustmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'adjustment_type_id' => $this->adjustment_type_id,
            'date' => $this->date,
            'reason_id' => $this->reason_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'description' => $this->description,
            'item_category_id' => $this->item_category_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'quantity' => $this->quantity,
        ];
    }
}
