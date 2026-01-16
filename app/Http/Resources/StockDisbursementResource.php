<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockDisbursementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'disbursement_id'       => $this->disbursement_id,
            'disbursement_number'   => $this->disbursement_number,
            'disbursement_date'     => $this->disbursement_date?->format('Y-m-d'),
            'disbursement_type'     => $this->disbursement_type,
            'branch_id'             => $this->branch_id,

            // Branch details
            'branch' => $this->whenLoaded('branch', fn () => [
                'id'   => $this->branch->id,
                'name' => $this->branch->name,
            ]),

            // Issued by
            'issued_by' => $this->issued_by,
            'issued_by_user' => $this->whenLoaded('issuedByUser', fn () => [
                'id'   => $this->issuedByUser->id,
                'name' => $this->issuedByUser->name,
            ]),

            // Approved by (can be null)
            'approved_by' => $this->approved_by,
            'approved_by_user' => $this->whenLoaded('approvedByUser', fn () => $this->approvedByUser ? [
                'id'   => $this->approvedByUser->id,
                'name' => $this->approvedByUser->name,
            ] : null),

            'remarks' => $this->remarks ?? null,

            // Items with product details
            'items' => StockDisbursementItemResource::collection(
                $this->whenLoaded('stockDisbursementItems')
            ),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}