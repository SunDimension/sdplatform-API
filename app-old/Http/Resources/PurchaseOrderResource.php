<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'po_id' => $this->po_id,
            'po_number' => $this->po_number,
            'supplier_id' => $this->supplier_id,
            'pr_id' => $this->pr_id,
            'order_date' => $this->order_date?->format('Y-m-d'),
            'order_date_formatted' => $this->order_date?->format('M d, Y'),
            'expected_delivery_date' => $this->expected_delivery_date?->format('Y-m-d'),
            'expected_delivery_date_formatted' => $this->expected_delivery_date?->format('M d, Y'),
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Supplier information
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    // 'id' => $this->supplier->id,
                    'supplier_id' => $this->supplier->supplier_id,
                    // 'name' => $this->supplier->name,
                    'supplier_name' => $this->supplier->supplier_name,
                    'contact_person' => $this->supplier->contact_person ?? null,
                    'phone' => $this->supplier->phone ?? null,
                    'email' => $this->supplier->email ?? null,
                ];
            }),

            'created_by' => $this->created_by,
            'created_by_user' => $this->whenLoaded('createdByUser', function () {
                return $this->createdByUser ? [
                    'id' => $this->createdByUser->id,
                    'name' => $this->createdByUser->name,
                    'email' => $this->createdByUser->email,
                ] : null;
            }),

            'approved_by' => $this->approved_by,
            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return $this->approvedByUser ? [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                    'email' => $this->approvedByUser->email,
                ] : null;
            }),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),

            // Order items with NESTED product information
            'order_items' => $this->whenLoaded('purchaseOrderItems', function () {
                return $this->purchaseOrderItems->map(function ($item) {
                    // Get the actual product name from the nested relationship
                    $productName = 'Unknown Product';
                    if ($item->product && $item->product->product) {
                        // CreateItem has the actual product name
                        $productName = $item->product->product->name
                            ?? $item->product->product->item_name
                            ?? $item->product->product->code
                            ?? 'Unknown Product';
                    }

                    return [
                        'po_item_id' => $item->po_item_id,
                        'po_id' => $item->po_id,
                        'product_id' => $item->product_id,
                        'quantity_ordered' => (float) $item->quantity_ordered,
                        'unit_price' => (float) $item->unit_price,
                        'amount' => (float) $item->amount,
                        'created_by' => $item->created_by,
                        'approved_by' => $item->approved_by,
                        'approved_at' => $item->approved_at?->format('Y-m-d H:i:s'),
                        'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at?->format('Y-m-d H:i:s'),
                        'product' => [
                            'id' => $item->product_id,
                            'name' => $productName,
                            'code' => $item->product?->product?->code ?? null,
                            'description' => $item->product?->product?->description ?? null,
                            'unit' => $item->product?->product?->unit ?? null,
                            'category' => $item->product?->product?->category ?? null,
                            // Include cost price from PurchaseItemCost
                            'cost_price' => $item->product?->new_cost_price ?? $item->unit_price,
                        ],
                    ];
                });
            }),

            // Computed total amount
            'total_amount' => $this->whenLoaded('purchaseOrderItems', function () {
                return $this->purchaseOrderItems->sum('amount');
            }),

            // Items count
            'items_count' => $this->whenLoaded('purchaseOrderItems', function () {
                return $this->purchaseOrderItems->count();
            }),
        ];
    }
}
