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
            'product_name' => $this->product ? $this->product->name : null,
            'sales_order_id' => $this->sales_order_id,
            'unit_price' => $this->unit_price ?? 0,
            'amount' => $this->amount ?? 0,
            'sales_date' => $this->sales_date,
            'store_id' => $this->store_id,
            'store_name' => $this->store ? $this->store->name : null,
            'quantity' => $this->quantity,
            'quantity_pieces' => $this->quantity_pieces,
            'unit_measurement' => $this->unit_measurement,
            'return_quantity' => $this->return_quantity ?? 0,
            'return_quantity_pieces' => $this->return_quantity_pieces ?? 0,
            'unit_measurement_name' => $this->whenLoaded('measurement', function () {
                return $this->measurement->name;
            }),
            'discount' => $this->discount ?? 0,
        ];
    }
}
