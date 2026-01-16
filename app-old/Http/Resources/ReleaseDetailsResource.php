<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseDetailsResource extends JsonResource
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
            'release_id' => $this->release_id,
            'quantity' => $this->release_quantity,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'unit_measurement' => $this->unit_measurement,
            'unit_measurement_name' => $this->unit_measurement_name,

            'amount' => $this->amount,
        ];
    }
}
