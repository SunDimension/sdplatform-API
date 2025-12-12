<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'description' => $this->description,
            'entry_date' => $this->entry_date?->format('Y-m-d') ?? null,
        ];
    }
}