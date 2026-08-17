<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyDocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required'],
            'survey_plan_url' => ['nullable', 'string'],
            'title' => ['required', 'string'],
            'CofO' => ['nullable', 'string'],
            'floor_plan' => ['nullable', 'string'],
            'approval_letter' => ['nullable', 'string'],
            'document_type' => ['required', 'string'],
            'verified' => ['required'],
            'document_url' => ['required', 'string'],
        ];
    }
}
