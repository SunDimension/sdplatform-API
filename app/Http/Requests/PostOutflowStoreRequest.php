<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostOutflowStoreRequest extends FormRequest
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
            'org_bank' => 'nullable|exists:banks,id', // Changed from required to nullable
            'customer_id' => ['required', 'string', 'exists:customers,id'],
            'amount' => 'nullable|numeric',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|numeric', // Changed from required to nullable
            'bene_bank' => 'nullable|exists:banks,id', // Changed from required to nullable
            'narration' => 'nullable|string',
            'outflow_date' => 'required|date',
            'outflow_mode' => 'required|exists:outflow_mode,id',
        ];
    }
}
