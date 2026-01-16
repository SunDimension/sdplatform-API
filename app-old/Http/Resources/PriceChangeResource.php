<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceChangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'details' => $this->details,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch->name,
            'change_reason_id' => $this->change_reason_id,
            'change_reason' => $this->changeReason->name,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approval_date' => $this->approval_date,
            'comment' => $this->comment,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
