<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChartUpdateRequest extends FormRequest
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
            'chart_title' => ['required', 'string'],
            'chart_type_id' => ['required', 'string'],
            'chart_category_id' => ['required', 'string'],
            'sql_query' => ['required', 'string'],
            'is_active' => ['required', 'string'],
            'module_id' => ['required', 'string'],
            'filterColumn' => ['required', 'string'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
