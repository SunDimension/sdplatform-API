<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalInstanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'approval_stage_id' => $this->approval_stage_id,
            'approval_type_id' => $this->approval_type_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
