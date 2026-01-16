<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chart_title' => $this->chart_title,
            'chart_type_id' => $this->chart_type_id,
            'chart_category_id' => $this->chart_category_id,
            'sql_query' => $this->sql_query,
            'is_active' => $this->is_active,
            'module_id' => $this->module_id,
            'filterColumn' => $this->filterColumn,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
