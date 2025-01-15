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
            'cash_discrepancy_id' => $this->cash_discrepancy_id,
            'discrepancy_amount' => $this->discrepancy_amount,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch ? $this->branch->name : null,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'approval_date'=>$this->approval_date,
            'approved_by'=>$this->approved_by,
            'amount' => $this->amount,
            'date' => $this->date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            
        ];
    }
}
