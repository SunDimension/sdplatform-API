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
            'store_id' => $this->store_id,
            'store_name' => $this->store->name ?? null, // Safe access
            'customer_name' => $this->customer->name,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_by_name' => $this->user->name,
            'approved_by' => $this->approved_by,
            'approved_by_name' => $this->when($this->approvedBy, function () {
                return $this->approvedBy->name;
            }),
            'approved_at' => $this->approved_at,
            'return_status' => $this->return_status,
            'approval_comment' => $this->approval_comment,
            'original_total_amount' => $this->original_total_amount ?? $this->total_amount,
            'calculated_return_amount' => $this->calculated_return_amount ?? 0,
            'items' => $this->returnDetails->map(function ($detail) {
                return [
                    'product_name' => $detail->product->name ?? '',
                    'return_quantity' => $detail->return_quantity,
                    'unit_price' => $detail->unit_price,
                    'discount' => $detail->discount ?? 0,
                    'total' => ($detail->return_quantity * ($detail->unit_price - ($detail->discount ?? 0))),
                    // add other fields as needed
                ];
            }),
            'total_discounted' => $this->returnDetails->sum(function ($detail) {
                return $detail->return_quantity * ($detail->unit_price - ($detail->discount ?? 0));
            }),
            'sales_receipt' => $this->whenLoaded('salesReceipt', function () {
                return new SalesReceiptResource($this->salesReceipt);
            }),
        ];
    }
}
