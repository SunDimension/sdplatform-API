<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostInflowResource extends JsonResource
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
            'bank_id' => $this->bank_id,
            'amount' => $this->amount,
            'narration' => $this->narration,
            'inflow_date' => $this->inflow_date,
            'inflow_status' => $this->inflow_status,
        ];
    }
}
