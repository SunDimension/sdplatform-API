<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'movement_id' => $this->movement_id,
            'product_id' => $this->product_id,
            'reference_type' => $this->reference_type ?? 'unknown',
            'reference_id' => $this->reference_id,
            'quantity_in' => (int) $this->quantity_in,
            'quantity_out' => (int) $this->quantity_out,
            'net_quantity' => (int) ($this->quantity_in - $this->quantity_out),
            'movement_date' => $this->movement_date?->format('Y-m-d'),
            'movement_date_formatted' => $this->movement_date?->format('M d, Y'),
            'movement_datetime' => $this->movement_date?->format('Y-m-d H:i:s'),
            'unit_cost' => (float) $this->unit_cost,
            'total_value' => (float) (($this->quantity_in - $this->quantity_out) * $this->unit_cost),

            // Product information - FIXED
            'product' => $this->whenLoaded('product', function () {
                // $this->product is PurchaseItemCost
                // $this->product->product is CreateItem
                return [
                    'id' => $this->product->product->id ?? null,
                    'name' => $this->product->product->name ?? 'Unknown Product',
                    'description' => $this->product->product->description ?? null,
                    'unit' => $this->product->product->unit ?? null,
                    'cost_price' => $this->product->new_cost_price ?? $this->unit_cost,
                ];
            }),

            // User information
            'created_by_user' => $this->whenLoaded('createdByUser', function () {
                return [
                    'id' => $this->createdByUser->id,
                    'name' => $this->createdByUser->name,
                    'email' => $this->createdByUser->email,
                ];
            }),

            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return $this->approvedByUser ? [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                    'email' => $this->approvedByUser->email,
                ] : null;
            }),

            // Computed fields for UI
            'movement_type' => $this->quantity_in > 0 ? 'in' : 'out',
            'movement_type_label' => $this->quantity_in > 0 ? 'Stock In' : 'Stock Out',

            'reference_display' => $this->getReferenceDisplay(),
        ];
    }

    /**
     * Get a human-readable reference display
     */
    private function getReferenceDisplay(): string
    {
        if (empty($this->reference_type)) {
            return 'Unknown';
        }

        return match (strtolower($this->reference_type)) {
            'goods_received', 'gr' => 'Goods Received',
            'disbursement', 'disb' => 'Disbursement',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'return' => 'Return',
            default => ucfirst(str_replace('_', ' ', $this->reference_type))
        };
    }
}
