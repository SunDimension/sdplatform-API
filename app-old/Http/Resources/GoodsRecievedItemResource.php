<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoodsRecievedItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gr_item_id' => $this->gr_item_id,
            'gr_id' => $this->gr_id,
            'po_item_id' => $this->po_item_id,
            'product_id' => $this->product_id,
            'quantity_received' => $this->quantity_received,
            'quantity_damaged' => $this->quantity_damaged,
            'batch_number' => $this->batch_number,
            'expiry_date' => $this->expiry_date,
            'sync_id' => $this->sync_id,
            'location_id' => $this->location_id,
            'sync_version' => $this->sync_version,
            'sync_status' => $this->sync_status,
            'last_synced_at' => $this->last_synced_at,
            'last_sync_attempt' => $this->last_sync_attempt,
            'sync_error' => $this->sync_error,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // ADD THIS: Product name at the top level
            'product_name' => $this->product?->product?->name ?? 'Unknown Product',

            // Purchase Order Item
            'purchase_order_item' => $this->purchaseOrderItem,

            // Product with nested relationship
            'product' => $this->product,
        ];
    }
}