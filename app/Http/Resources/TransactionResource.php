<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'financial_period_id' => $this->financial_period_id,
            'transaction_date' => $this->transaction_date,
            'transcode' => $this->transcode,
            'transtype' => $this->transtype,
            'naration' => $this->naration,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'amount' => $this->amount,
            'warehouse_id' => $this->warehouse_id,
            'account_no' => $this->account_no,
            'account_id' => $this->account_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
