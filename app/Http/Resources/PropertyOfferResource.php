<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'buyer_id' => $this->buyer_id,
            'offer_amount' => $this->offer_amount,
            'status' => $this->status,
            'message' => $this->message,
            'accepted_at' => $this->accepted_at,
            'rejected_at' => $this->rejected_at,
        ];
    }
}
