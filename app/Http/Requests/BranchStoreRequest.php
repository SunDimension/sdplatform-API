<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchStoreRequest extends FormRequest
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
            'address' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
