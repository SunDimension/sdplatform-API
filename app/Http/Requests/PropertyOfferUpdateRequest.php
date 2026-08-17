<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyOfferUpdateRequest extends FormRequest
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
            'property_id' => ['required'],
            'buyer_id' => ['required'],
            'offer_amount' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'message' => ['nullable', 'string'],
            'accepted_at' => ['nullable'],
            'rejected_at' => ['nullable'],
        ];
    }
}
