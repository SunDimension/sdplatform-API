<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChartOfAccountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255'],
            'account_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ledger_accounts', 'account_code')->ignore($this->route('chart_of_account'), 'account_id'),
            ],
            'account_type_id' => ['required', 'exists:account_type,account_type_id'],
        ];
    }
}
