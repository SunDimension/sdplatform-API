<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'account_group_id' => ['required', 'integer', 'exists:account_groups,id'],
            'account_type_id' => ['required', 'integer', 'exists:account_types,id'],
            'account_subtype_id' => ['required', 'integer', 'exists:account_subtypes,id'],
            'account_owner_id' => ['required', 'string'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
