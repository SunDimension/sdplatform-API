<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'payment_date' => $this->payment_date,
            'warehouse_id' => $this->warehouse_id,
            'vendor_id' => $this->vendor_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'journalEntryDetails' => JournalEntryDetailCollection::make($this->whenLoaded('journalEntryDetails')),
        ];
    }
}
