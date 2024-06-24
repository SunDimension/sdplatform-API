<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
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
            'customer_type_id' => ['required', 'integer', 'exists:customer_types,id'],
            'title_id' => ['required', 'integer', 'exists:titles,id'],
            'surname' => ['required', 'string'],
            'firstname' => ['required', 'string'],
            'middlename' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'fullname' => ['required', 'string'],
        ];
    }
}
