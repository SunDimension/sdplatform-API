<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialYearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'financialPeriods' => FinancialPeriodCollection::make($this->whenLoaded('financialPeriods')),
        ];
    }
}
