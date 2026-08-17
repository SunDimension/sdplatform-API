<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'payment_id' => $this->payment_id,
        ];
    }
}
