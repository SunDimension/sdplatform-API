<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $this->user_id,
            'scheduled_at' => $this->scheduled_at,
            'schedule_time' => $this->schedule_time,
            'remarks' => $this->remarks,
            'status' => $this->status,
        ];
    }
}
