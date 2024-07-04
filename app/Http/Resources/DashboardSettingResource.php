<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chart_id' => $this->chart_id,
            'module_id' => $this->module_id,
            'chart_type_id' => $this->chart_type_id,
            'chart_category_id' => $this->chart_category_id,
            'chart_title' => $this->chart_title,
            'is_active' => $this->is_active,
            'order_by' => $this->order_by,
            'is_group' => $this->is_group,
            'submodule_Id' => $this->submodule_Id,
            'add_condition' => $this->add_condition,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
