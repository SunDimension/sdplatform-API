<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'card_title' => $this->card_title,
            'card_size' => $this->card_size,
            'is_active' => $this->is_active,
            'sql_query' => $this->sql_query,
            'module_id' => $this->module_id,
            'submodule_id' => $this->submodule_id,
            'sequence' => $this->sequence,
            'color' => $this->color,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
