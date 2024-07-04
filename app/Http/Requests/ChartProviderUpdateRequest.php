<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChartProviderUpdateRequest extends FormRequest
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
            'chart_provider' => ['required', 'string'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
