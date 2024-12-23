<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiveOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_order_number' => $this->purchase_order_number,
            'receive_date' => $this->receive_date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->vendor->name,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch->name,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'receiveItems' => ReceiveItemResource::collection($this->whenLoaded('receiveItems')),
        ];
    }
}
