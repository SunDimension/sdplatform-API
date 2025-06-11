<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnItemResource extends JsonResource
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
            'sales_receipt_id' => $this->sales_receipt_id,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch->name,
            'return_date' => $this->return_date,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer->name,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_by_name' => $this->user->name,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'return_status' => $this->return_status,
            'approval_comment' => $this->approval_comment,
            'items' => ReturnDetailsResource::collection($this->returnDetails),
            
            // Include the full sales receipt details
'sales_receipt' => $this->whenLoaded('salesReceipt', function () {
    return new SalesReceiptResource($this->salesReceipt);
}),
        ];
    }
}