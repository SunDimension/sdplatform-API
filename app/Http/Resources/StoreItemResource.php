<?php

namespace App\Http\Resources;

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
            'item_category_id'=>$this->item_category_id,
            // 'item_category_name' => $this->item_category ? $this->item_category->name : null,
            'create_item_id'=>$this->create_item_id,
            // 'create_item_name' => $this->create_item ? $this->create_item->name : null,
            'unit_id'=>$this->unit_id, 
            // 'unit_name' => $this->unit ? $this->unit->name : null,
            'cost_price'=>$this->cost_price, 
            'selling_price'=>$this->selling_price, 
            'reorder_level'=>$this->reorder_level, 
            'quantity'=>$this->quantity - $this->quantity_holding, 
            'quantity_holding'=>$this->quantity_holding, 
            'store_id'=>$this->store_id,
            // 'store_name' => $this->store ? $this->store->name : null,  
            'user_id'=>$this->user_id,
           
            'discount'=>$this->discount,
            'store_type_id'=>$this->store_type_id,
            // 'store_type_name' => $this->store_type ? $this->store_type->name : null,
        ];
    }
}
