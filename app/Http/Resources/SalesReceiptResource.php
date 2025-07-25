<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $details = is_string($this->payment_detail) ? json_decode($this->payment_detail, true) : $this->payment_detail;

        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer ? $this->customer->name : null,
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'store_id' => $this->store_id,
            'store_name' => $this->store ? $this->store->name : null,
            'cashier_id'=>$this->cashier_id,
            'user_id' => $this->salesOrder->user->name ?? null, // Sales Rep
            'cashier_name' => $this->cashier->name ?? null,
            'sales_receipt_number' => $this->sales_receipt_number,
            'payment_type' => $this->payment_type,
            'sales_order_id' => $this->sales_order_id,
            'sales_order' =>  new SalesOrderResource($this->whenLoaded('salesOrder')),
            //'items_sold' => ItemSoldResource::collection($this->whenLoaded('itemsSold')),
            //'sales_invoice' => $this->sales_invoice,
            'amount_paid' => $this->amount_paid,
            'unit_measurement'=>$this->unit_measurement,
            'receipt_date' => $this->receipt_date,
            'total_amount' => $this->total_amount,
            'payment_detail' => $details,
            //'cash'
            'return_items' => $this->whenLoaded('returnItems', function () {
            return $this->returnItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'return_date' => $item->return_date,
                    'return_status' => $item->return_status,
                    'return_details' => $item->returnDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'product_id' => $detail->product_id,
                            'product_name' => $detail->product->name ?? null,
                            'return_quantity' => $detail->return_quantity,
                            'unit_price' => $detail->unit_price,
                            'amount' => $detail->return_quantity * $detail->unit_price
                        ];
                    })
                ];
            });
        }),
            
        ];
    }
}
