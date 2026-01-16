<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoodsReceivedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gr_id' => $this->gr_id,
            'gr_number' => $this->gr_number,
            'po_id' => $this->po_id,
            'received_date' => $this->received_date,
            'invoice_status' => $this->invoice_status,
            'received_date_formatted' => $this->received_date?->format('M d, Y'),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'purchase_order' => $this->whenLoaded('purchaseOrder', function () {
                return [
                    'po_id' => $this->purchaseOrder->po_id,
                    'po_number' => $this->purchaseOrder->po_number,
                    'order_date' => $this->purchaseOrder->order_date?->format('Y-m-d'),
                    'expected_delivery_date' => $this->purchaseOrder->expected_delivery_date?->format('Y-m-d'),
                    'status' => $this->purchaseOrder->status,
                    'supplier' => $this->whenLoaded('purchaseOrder.supplier', function () {
                        return [
                            'id' => $this->purchaseOrder->supplier->id,
                            'supplier_name' => $this->purchaseOrder->supplier->supplier_name,
                            'contact_person' => $this->purchaseOrder->supplier->contact_person ?? null,
                            'phone' => $this->purchaseOrder->supplier->phone ?? null,
                            'email' => $this->purchaseOrder->supplier->email ?? null,
                        ];
                    }),
                ];
            }),

            'received_by_user' => $this->whenLoaded('recievedByUser', function () {
                return [
                    'id' => $this->recievedByUser->id,
                    'name' => $this->recievedByUser->name,
                    'email' => $this->recievedByUser->email,
                ];
            }),

            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return $this->approvedByUser ? [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                    'email' => $this->approvedByUser->email,
                ] : null;
            }),

            // Goods Received Items
            // 'items' => $this->whenLoaded('goodsReceivedItems', function () {
            //     return GoodRecievedItemResource::collection($this->goodsReceivedItems);
            // }),

            'items' => GoodsRecievedItemResource::collection($this->whenLoaded('items')),

            // Computed fields for Vue.js UI
            'total_items_count' => $this->whenLoaded('goodsReceivedItems', function () {
                return $this->goodsReceivedItems->count();
            }),

            'total_quantity_received' => $this->whenLoaded('goodsReceivedItems', function () {
                return $this->goodsReceivedItems->sum('quantity_received');
            }),

            'total_quantity_damaged' => $this->whenLoaded('goodsReceivedItems', function () {
                return $this->goodsReceivedItems->sum('quantity_damaged');
            }),

            'total_usable_quantity' => $this->whenLoaded('goodsReceivedItems', function () {
                return $this->goodsReceivedItems->sum(function ($item) {
                    return $item->quantity_received - $item->quantity_damaged;
                });
            }),
        ];
    }
}

// File: app/Http/Resources/GoodsReceivedCollection.php



// File: app/Http/Resources/GoodRecievedItemResource.php
