<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankRemittanceUpdateRequest extends FormRequest
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
           
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'amount' => ['required', 'numeric'],
            'account_number' => ['required', 'string'],
            'date' => ['required', 'date'],
            

           
        ];
    }
}
