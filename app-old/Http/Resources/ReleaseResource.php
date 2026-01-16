<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
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
            'customer_name' => $this->customer ? $this->customer->name : null,
            // 'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch ? $this->branch->name : null,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'store_name' => $this->store ? $this->store->name : null,
            'user_name' => $this->user ? $this->user->name : null,
            'created_at' => $this->created_at,
            'updated_at'=>$this->updated_at,
            'release_date'=>$this->release_date,
            'items'=> ReleaseDetailsResource::collection($this->whenLoaded('releasedetail')),
        ];
    }
}
