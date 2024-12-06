<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemPriceResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'selling_price' => $this->selling_price,
            'change_date' => $this->change_date,
            'store_item_id' => $this->store_item_id,
            'user_id' => $this->user_id,
            'user_name'=>$this->user->name,
            
        ];
    }
}
