<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChartTypeStoreRequest extends FormRequest
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
            'chart_category_id' => ['required', 'integer', 'exists:chart_categories,id'],
            'chart_type' => ['required', 'string'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
