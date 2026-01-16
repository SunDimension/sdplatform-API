<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockMovementCollection extends ResourceCollection
{
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
                'total_quantity_in' => $this->collection->sum('quantity_in'),
                'total_quantity_out' => $this->collection->sum('quantity_out'),
                'net_quantity' => $this->collection->sum(function($item) {
                    return $item->quantity_in - $item->quantity_out;
                }),
                'total_value' => $this->collection->sum(function($item) {
                    return ($item->quantity_in - $item->quantity_out) * $item->unit_cost;
                }),
            ],
        ];
    }
}