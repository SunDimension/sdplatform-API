<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorStoreRequest extends FormRequest
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
            'title' => ['required', 'integer', 'exists:titles,id'],
            'designation' => ['required', 'integer', 'exists:designations,id'],
            'contact_surname' => ['required', 'string'],
            'contact_firstname' => ['required', 'string'],
            'contact_middlename' => ['required', 'string'],
            'vendor_type' => ['required', 'integer', 'exists:vendor_types,id'],
             'service_type' => ['required', 'integer', 'exists:service_types,id'],
            'contact_phone_number' => ['required', 'string'],
            'contact_email' => ['nullable', 'email'],
            'image_url' => ['nullable', 'string'],
            'tin' => ['required', 'string'],
            'bank' => ['required', 'integer', 'exists:banks,id'],
            'account_number' => ['nullable', 'string'],
            'account_name' => ['nullable', 'string'],
        ];
    }
}
