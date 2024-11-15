<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderResource extends JsonResource
{
    private $extraParam;

    public function __construct($resource, $extraParam = null) {
        parent::__construct($resource);
        $this->extraParam = $extraParam;
    }
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
            'customer_name' => $this->customer ? $this->customer->name : null,
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch ? $this->branch->name : null,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'store_name' => $this->store ? $this->store->name : null,
            'user_name' => $this->user ? $this->user->name : null,
            'sales_order_number' => $this->sales_order_number,
            'credit_limit' => $this->credit_limit,
            'credit_balance' => $this->credit_balance,
            'total_amount' => $this->total_amount,
            'payment_type' => $this->payment_type,
            'created_at' => $this->created_at,
            'status'=>$this->status,
            'updated_at'=>$this->updated_at,
            'items'=> ItemSoldResource::collection($this->whenLoaded('itemSold')),
            'deposit'=> $this->extraParam == null ? 0 : $this->extraParam,
        ];
    }
}
