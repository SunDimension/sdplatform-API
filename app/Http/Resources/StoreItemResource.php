<?php

namespace App\Http\Resources;

use App\Classes\StockUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreItemResource extends JsonResource
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
            'name' => $this->createItem->name,
            'create_item_name' => $this->createItem->name,
            'item_category_id' => $this->item_category_id,
            'create_item_id' => $this->create_item_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'reorder_level' => $this->reorder_level,
            'quantity' => $this->quantity - $this->quantity_holding,
            'quantity_holding' => $this->quantity_holding,
            'quantity_actual' => StockUtil::getActualQuantity($this->createItem->id, $this->store_id),
            'quantity_request' => StockUtil::getQuantityForRequest($this->createItem->id, $this->store_id),
            'store_id' => $this->store_id,
            'branch_id' => $this->branch_id,
            'discount' => $this->discount,
            'set_limit' => $this->set_limit,
            'quantity_in_package' => $this->quantity_in_package, // Add this line
        'selling_price_per_unit' => $this->selling_price_per_unit, 


        ];
    }
}
