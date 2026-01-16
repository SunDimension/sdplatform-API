<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bank_id' => $this->name,
            'inflow_id'=>$this->inflow_id,
            'outflow_id'=>$this->outflow_id,
            'cheque_in_transit'=>$this->cheque_in_transit
        ];
    }
}
