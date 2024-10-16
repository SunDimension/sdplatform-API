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
            'create_item_id'=>$this->create_item_id,
            'unit_id'=>$this->unit_id, 
            'cost_price'=>$this->cost_price, 
            'selling_price'=>$this->selling_price, 
            'reorder_level'=>$this->reorder_level, 
            'Quantity'=>$this->Quantity, 
            'store_id'=>$this->store_id,  
            'user_id'=>$this->branch_id,
            'discount'=>$this->branch_id,
            'store_type_id'=>$this->store_type_id
        ];
    }
}
