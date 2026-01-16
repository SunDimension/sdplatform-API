<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesInvoiceResource extends JsonResource
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
            'sales_order' => $this->sales_order,
            'sales_invoice_number' => $this->sales_invoice_number,
            'invoice_amount' => $this->invoice_amount,
            'invoice_date' => $this->receipt_date,
        
        ];
    }
}
