<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'survey_plan_url' => $this->survey_plan_url,
            'title' => $this->title,
            'CofO' => $this->CofO,
            'floor_plan' => $this->floor_plan,
            'approval_letter' => $this->approval_letter,
            'document_type' => $this->document_type,
            'verified' => $this->verified,
            'document_url' => $this->document_url,
        ];
    }
}
