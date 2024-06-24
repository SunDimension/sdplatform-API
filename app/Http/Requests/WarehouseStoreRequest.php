<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoreRequest extends FormRequest
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
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse_address' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
        ];
    }
}
