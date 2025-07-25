<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'customer_id' => $this->customer_id,
            'sales_order_id' => $this->sales_order_id,
            'sales_receipt_id' => $this->sales_receipt_id,
            'amount' => $this->amount,
            'credit_limit' => $this->credit_limit,
            'credit_balance_before' => $this->credit_balance_before,
            'type' => $this->type,
            'credit_order_number' => $this->credit_order_number,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'salesReceipts' => SalesReceiptCollection::make($this->whenLoaded('salesReceipts')),
        ];
    }
}
