<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockDisbursementItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'disbursement_item_id' => $this->disbursement_item_id,
            'product_id'           => $this->product_id,

            // Product details
            'product' => $this->whenLoaded('product', fn() => [
                'id'   => $this->product->id,
                'name' => $this->product->name,
                'code' => $this->product->code ?? null,
                // Add other fields you need: unit, category, etc.
            ]),

            'quantity_issued'   => (int) $this->quantity_issued,
            'quantity_damaged'  => (int) ($this->quantity_damaged ?? 0),
            'usable_quantity'   => (int) ($this->quantity_issued - ($this->quantity_damaged ?? 0)),
            'unit_cost'         => (float) $this->unit_cost,
            'total_cost'        => (float) ($this->unit_cost * $this->quantity_issued),

            'batch_number'      => $this->batch_number,
            'expiry_date'       => $this->expiry_date?->format('Y-m-d'),

            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
