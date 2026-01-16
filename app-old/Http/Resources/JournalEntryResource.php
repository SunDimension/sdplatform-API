<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SalesOrder;

class JournalEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'journal_id' => $this->journal_id,
            'description' => $this->getCleanDescription(),
            'entry_date' => $this->entry_date?->format('Y-m-d') ?? null,
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
