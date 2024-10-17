<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemSoldResource extends JsonResource
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
            'sales_order' => $this->sales_order,
            'unit_price' => $this->unit_price,
            'amount' => $this->amount,
            'sales_date' => $this->sales_date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            'quantity' => $this->quantity,
            'discount' => $this->discount ?? 0,
        ];
    }
}
