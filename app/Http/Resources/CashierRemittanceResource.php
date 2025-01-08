<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashierRemittanceResource extends JsonResource
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
            'branch_id' => $this->branch_id,
      
            'user_id' => $this->user_id,
            
            'amount' => $this->amount,
            'date' => $this->date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            
        ];
    }
}
