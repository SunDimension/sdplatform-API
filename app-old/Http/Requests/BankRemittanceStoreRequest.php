<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankRemittanceStoreRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'exists:users,id'],
            'bank_id' => ['required', 'string', 'exists:banks,id'],
            'amount' => ['required'],
            'account_number' => ['required', 'numeric'],
            'date' => ['required', 'date'],



        ];
    }
}
