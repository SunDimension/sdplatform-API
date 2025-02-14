<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItemResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->createItem->name .' - '. $this->store->name,
            'create_item_name' => $this->createItem->name,
            'item_category_id' => $this->item_category_id,
            'create_item_id' => $this->create_item_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'reorder_level' => $this->reorder_level,
            'quantity' => $this->quantity - $this->quantity_holding,
            'quantity_holding' => $this->quantity_holding,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            'branch_id' => $this->branch_id,
            'discount' => $this->discount,
            'set_limit' => $this->set_limit,


        ];
    }
}
