<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           'account_id' => $this->account_id,
            // 'account_name' => $this->account?->name,
            'account_name' => $this->account ? $this->account->account_name : null,
            'debit_amount' => $this->debit_amount,
            'credit_amount' => $this->credit_amount,
            'created_at' => $this->created_at,
        ];
    }
}