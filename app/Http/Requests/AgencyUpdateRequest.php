<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgencyUpdateRequest extends FormRequest
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
            'company_name' => ['required', 'string'],
            'registeration_number' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'status' => ['required', 'string'],
            'website' => ['nullable', 'string'],
            'license_number' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'subscription_id' => ['nullable'],
        ];
    }
}
