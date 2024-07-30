<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'item_category_id' => $this->item_category_id,
            'item_type_id' => $this->item_type_id,
            'description' => $this->description,
            'batch_number' => $this->batch_number,
            'unit_id' => $this->unit_id,
            'brand_id' => $this->brand_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'reorder_level' => $this->reorder_level,
            'quantity' => $this->quantity,
            'dimension_id' => $this->dimension_id,
            'weight_id' => $this->weight_id,
            'branch_id' => $this->branch_id,
            'store_id'=> $this->store_id,
            'warehouse' => $this->warehouse,
            'vendor_id' => $this->vendor_id,
            'image_url' => $this->image_url,
            'barcode' => $this->barcode,
        ];
    }
}
