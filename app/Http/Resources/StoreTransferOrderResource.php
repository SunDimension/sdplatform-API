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
            'source_branch_id' => $this->source_branch_id,
            'source_branch' => $this->sourceBranch->name,
            'source_store_id' => $this->source_store_id,
            'source_store' => $this->sourceStore->name,
            'destination_branch_id' => $this->destination_branch_id,
            'destination_branch' => $this->destinationBranch->name,
            'destination_store_id' => $this->destination_store_id,
            'destination_store' => $this->destinationStore->name,
            'approval_stage_id' => $this->approval_stage_id,
            'source_status' => $this->source_status,
            'source_date_approved' => $this->source_date_approved,
            'destination_status' => $this->destination_status,
            'destination_date_approved' => $this->destination_date_approved,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'items' => StoreTransferItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
