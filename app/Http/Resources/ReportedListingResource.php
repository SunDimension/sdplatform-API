<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportedListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'reporter_id' => $this->reporter_id,
            'reason' => $this->reason,
            'description' => $this->description,
            'resolved_by' => $this->resolved_by,
            'resolved_at' => $this->resolved_at,
            'status' => $this->status,
        ];
    }
}
