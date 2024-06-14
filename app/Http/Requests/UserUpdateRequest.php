<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'user_name' => ['required', 'string'],
            'user_email' => ['required', 'string'],
            'password' => ['required', 'password'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
        ];
    }
}
