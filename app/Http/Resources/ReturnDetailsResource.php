<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnDetailsResource extends JsonResource
{
 public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'product_id' => $this->product_id,
        'product_name' => $this->product->name ?? null,
        'return_quantity' => $this->return_quantity,
        'quantity_returned_pieces' => $this->return_quantity_pieces,
        'unit_price' => $this->unit_price,
        'store_id' => $this->store_id,
        'store_name' => $this->store->name ?? null,
        'unit_measurement' => $this->unit_measurement,
        'item_sold_id' => $this->item_sold_id,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
}