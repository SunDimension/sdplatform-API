<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer->name,
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch->name,
            'store_id' => $this->store_id,
            'store_name'=> $this->store->name,
            'sales_order_number' => $this->sales_order_number,
            'credit_limit' => $this->credit_limit,
            'credit_balance' => $this->credit_balance,
            'total_amount' => $this->total_amount,
            'payment_type' => $this->payment_type,
            'status'=>$this->status,
            'items'=> ItemSoldResource::collection($this->itemsold),

        ];
    }
}
