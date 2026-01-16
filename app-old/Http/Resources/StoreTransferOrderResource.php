<?php

namespace App\Http\Resources;

use App\Models\StoreTransferItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreTransferOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'transfer_date' => $this->transfer_date,
            'source_store_id' => $this->source_store_id,
            'source_store' => $this->sourceStore->name,
            'destination_store_id' => $this->destination_store_id,
            'destination_store' => $this->destinationStore->name,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'items' => StoreTransferItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
