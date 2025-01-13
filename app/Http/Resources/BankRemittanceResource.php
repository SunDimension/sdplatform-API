<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankRemittanceResource extends JsonResource
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
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch ? $this->branch->name : null,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'account_number' => $this->account_number,
            'amount' => $this->amount,
            'date' => $this->date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            
        ];
    }
}
