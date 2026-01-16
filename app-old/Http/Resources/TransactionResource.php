<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SalesOrder;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? $this->transaction_id, // Add a numeric ID for frontend
            'transaction_id' => $this->transaction_id,
            'transaction_type' => $this->getReadableTransactionType(),
            'description' => $this->getCleanDescription(),
            'transaction_date' => $this->transaction_date?->format('Y-m-d'), // Clean date format
            'total_amount' => number_format((float)$this->total_amount, 2, '.', ','), // Formatted amount
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get a clean, user-friendly description without UUIDs
     */
    private function getCleanDescription(): string
    {
        $description = $this->description;

        // If description contains a UUID, try to replace it with a readable reference
        if (preg_match('/#([a-f0-9\-]{36})/', $description, $matches)) {
            $uuid = $matches[1];
            
            // Try to find the sales order with this UUID
            $salesOrder = SalesOrder::where('id', $uuid)->first();
            
            if ($salesOrder) {
                // Replace UUID with sales order number
                $description = preg_replace(
                    '/#[a-f0-9\-]{36}/',
                    "#{$salesOrder->sales_order_number}",
                    $description
                );
            } else {
                // If no sales order found, just remove the UUID
                $description = preg_replace('/#[a-f0-9\-]{36}/', '', $description);
                $description = trim($description);
            }
        }

        return $description;
    }

    /**
     * Get a more readable transaction type
     */
    private function getReadableTransactionType(): string
    {
        $types = [
            'SALE' => 'Sale',
            'SALE_RETURN' => 'Sale Return',
            'SALE_REVERSAL' => 'Sale Reversal',
            'PAYMENT' => 'Payment',
            'REFUND' => 'Refund',
        ];

        return $types[$this->transaction_type] ?? $this->transaction_type;
    }
}