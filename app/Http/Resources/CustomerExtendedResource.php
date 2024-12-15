<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerExtendedResource extends JsonResource
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
            'surname' => $this->surname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'name' => trim($this->surname . ' ' . $this->firstname), 
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'customer_type_id' => $this->customer_type_id,
            'title_id' => $this->title_id,
            'branch_id'=>$this->branch_id,
            'branch_name'=>$this->branch_name,
            'credit_limit' => $this->credit_limit,
            'credit_balance' => $this->credit_balance ??$this->credit_limit,
            'total_credit' => $this->total_credit ?? 0,
            'total_payment' => $this->total_payment ?? 0,
            'balance' => ($this->total_payment ?? 0) - ($this->total_payment ?? 0),
            'total_inflow' => $this->total_inflow ?? 0,
            'total_outflow' => $this->total_outflow ?? 0,
            'deposit_balance' => $this->total_inflow ?? 0 - $this->total_outflow ?? 0,
            'inflows' => PostInflowResource::collection($this->whenLoaded("inflows")),
            'outflows' => PostOutflowResource::collection($this->whenLoaded("outflows")),
            'creditTransactions' => CreditTransactionResource::collection($this->whenLoaded("creditTransactions")), 
        ];
    }
}
