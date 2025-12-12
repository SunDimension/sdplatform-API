<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChartOfAccountStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255'],
            'account_code' => ['required', 'string', 'max:20', 'unique:ledger_accounts,account_code'],
            'account_type_id' => ['required', 'exists:account_type,account_type_id'],
        ];
    }
}
