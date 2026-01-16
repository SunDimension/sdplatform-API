<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorTargetUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'integer', 'exists:vendor_tables,id'],
            'year' =>['required','integer','exists:years,id'],
            'quantity' => ['string'],
            'value' => ['string'],
            'product_id' => ['required', 'integer', 'exists:createItem,id'],
            
        ];
    }
}
