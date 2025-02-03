<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashierExpenseResource extends JsonResource
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
            'branch_name' => $this->branch ? $this->branch->name : null,
            'expense_line_id' => $this->expense_line_id,
            'expense_line' => $this->expense->name,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'amount' => $this->amount,
            'narration' => $this->narration,
            'date' => $this->date,
            'store_id' => $this->store_id,
            'store_name' => $this->store->name,
            'approval_date'=>$this->approval_date,
            'approval_by'=>$this->approval_by,
            
        ];
    }
}
