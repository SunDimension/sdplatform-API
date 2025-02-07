<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChartCardStoreRequest extends FormRequest
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
            'card_title' => ['required', 'string'],
            'card_size' => ['required', 'string'],
            'is_active' => ['required', 'string'],
            'sql_query' => ['required', 'string'],
            'module_id' => ['required', 'string'],
            'submodule_id' => ['required', 'string'],
            'sequence' => ['required', 'string'],
            'color' => ['required', 'string'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
