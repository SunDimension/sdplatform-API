<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SupplierInvoiceCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = SupplierInvoiceResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'total_amount' => $this->collection->sum('total_amount'),
                'paid_count' => $this->collection->where('status', 'paid')->count(),
                'unpaid_count' => $this->collection->where('status', 'unpaid')->count(),
                'overdue_count' => $this->collection->filter(function ($invoice) {
                    return $invoice->resource->isOverdue();
                })->count(),
                'total_overdue_amount' => $this->collection->filter(function ($invoice) {
                    return $invoice->resource->isOverdue();
                })->sum('total_amount'),
            ],
            'links' => [
                'self' => $request->fullUrl(),
            ],
        ];
    }

    /**
     * Customize the response for the request.
     */
    public function withResponse($request, $response): void
    {
        $response->header('X-API-Version', '1.0');
        $response->header('X-API-Resource', 'SupplierInvoices');
    }
}
