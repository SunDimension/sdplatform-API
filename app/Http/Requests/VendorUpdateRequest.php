<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateRequest extends FormRequest
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
            'contact_title' => ['required', 'string'],
            'contact_designation' => ['required', 'string'],
            'contact_surname' => ['required', 'string'],
            'contact_firstname' => ['required', 'string'],
            'contact_middlename' => ['required', 'string'],
            'contact_fullname' => ['required', 'string'],
            'vendor_type_id' => ['required', 'integer', 'exists:vendor_types,id'],
            'phone_number' => ['required', 'string'],
            'email' => ['required', 'email'],
            'image_url' => ['required', 'string'],
            'tin' => ['required', 'string'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'account_number' => ['required', 'string'],
        ];
    }
}
