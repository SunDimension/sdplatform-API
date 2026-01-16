<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GoodsRecievedCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($goodsReceived) {
                return [
                    'gr_id' => $goodsReceived->gr_id,
                    'gr_number' => $goodsReceived->gr_number,
                    'po_id' => $goodsReceived->po_id,
                    'recieved_date' => $goodsReceived->recieved_date,
                    'received_by' => $goodsReceived->received_by,
                    'status' => $goodsReceived->status,
                    'remarks' => $goodsReceived->remarks,
                    'sync_id' => $goodsReceived->sync_id,
                    'location_id' => $goodsReceived->location_id,
                    'sync_version' => $goodsReceived->sync_version,
                    'sync_status' => $goodsReceived->sync_status,
                    'last_synced_at' => $goodsReceived->last_synced_at,
                    'last_sync_attempt' => $goodsReceived->last_sync_attempt,
                    'sync_error' => $goodsReceived->sync_error,
                    'created_at' => $goodsReceived->created_at,
                    'updated_at' => $goodsReceived->updated_at,
                    'invoice_status' => $goodsReceived->invoice_status,

                    // Purchase Order
                    'purchase_order' => $goodsReceived->purchaseOrder,
                    'purchase_order_items' => $goodsReceived->purchaseOrderItems,
                    'recieved_by_user' => $goodsReceived->recievedByUser,

                    // THIS IS THE KEY FIX: Use GoodsRecievedItemResource for items
                    'items' => GoodsRecievedItemResource::collection($goodsReceived->items),
                ];
            }),
            'meta' => [
                'total' => $this->collection->count(),
                'total_received_quantity' => $this->collection->sum(function ($gr) {
                    return $gr->items->sum('quantity_received');
                }),
                'total_damaged_quantity' => $this->collection->sum(function ($gr) {
                    return $gr->items->sum('quantity_damaged');
                }),
            ],
        ];
    }
}
