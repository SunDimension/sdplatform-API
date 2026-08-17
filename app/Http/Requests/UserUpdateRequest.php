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
            'role_id' => ['required'],
            'agency_id' => ['nullable'],
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'password' => ['required', 'password'],
            'profile_picture' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'email_verified' => ['required'],
            'phone_verified' => ['required'],
            'kyc_verified' => ['required'],
            'last_login' => ['nullable'],
            'remember_token' => ['nullable', 'string'],
        ];
    }
}
