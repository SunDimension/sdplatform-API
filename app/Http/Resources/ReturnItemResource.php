<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnItemResource extends JsonResource
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
            'sales_receipt_id' => $this->sales_receipt_id,
            'branch_id' => $this->branch_id,
            'return_date' => $this->return_date,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'return_status' => $this->return_status,
            'approval_comment' => $this->approval_comment,
            'items' => ReturnDetailsResource::collection($this->returnDetails),
        ];
    }
}
