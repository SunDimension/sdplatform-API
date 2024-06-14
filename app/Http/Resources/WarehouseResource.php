<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'warehouse_address' => $this->warehouse_address,
            'zipcode' => $this->zipcode,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
