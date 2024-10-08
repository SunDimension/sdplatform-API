<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostOutflowResource extends JsonResource
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
            'org_bank' => $this->org_bank,
            'beneficiary' => $this->beneficiary,
            'amount' => $this->amount,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'bene_bank' => $this->bene_bank,
            'narration' => $this->narration,
            'outflow_date' => $this->outflow_date,
             'outflow_mode' => $this->outflow_mode,
        ];
    }
}
