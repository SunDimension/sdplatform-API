<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'old_cost_price' => $this->old_cost_price,
            'new_cost_price' => $this->new_cost_price,
            'product_id' => $this->product_id,
            'product_name' => $this->product ? $this->product->name : null,

        ];
    }
}
