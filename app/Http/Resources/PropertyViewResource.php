<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyViewResource extends JsonResource
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
            'ip_address' => $this->ip_address,
            'device' => $this->device,
            'browser' => $this->browser,
            'country' => $this->country,
            'viewed_at' => $this->viewed_at,
        ];
    }
}
