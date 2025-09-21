<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            // 'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            // 'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
        ];
    }
}
