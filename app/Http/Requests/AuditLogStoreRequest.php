<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditLogStoreRequest extends FormRequest
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
            'user_id' => ['nullable'],
            'action' => ['required', 'string'],
            'model' => ['required', 'string'],
            'model_id' => ['nullable'],
            'changes' => ['nullable', 'string'],
            'old_values' => ['nullable', 'string'],
            'new_values' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'string'],
            'user_agent' => ['nullable', 'string'],
        ];
    }
}
