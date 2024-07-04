<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardSettingStoreRequest extends FormRequest
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
            'chart_id' => ['required', 'integer', 'exists:charts,id'],
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'chart_type_id' => ['required', 'integer', 'exists:chart_types,id'],
            'chart_category_id' => ['required', 'integer', 'exists:chart_categories,id'],
            'chart_title' => ['required', 'string'],
            'is_active' => ['required', 'string'],
            'order_by' => ['required', 'string'],
            'is_group' => ['required', 'string'],
            'submodule_Id' => ['required', 'string'],
            'add_condition' => ['required', 'string'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
