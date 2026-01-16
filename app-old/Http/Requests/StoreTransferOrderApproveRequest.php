<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferOrderApproveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add your authorization logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:approved,rejected,pending'],
            'id' => ['required', 'string', 'exists:store_transfer_orders,id'],
            'source' => ['required', 'string', 'in:source,destination'],
            'stage' => ['required', 'string', 'in:store,branch']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of: approved, rejected, pending.',
            'source.in' => 'The source must be either "source" or "destination".',
            'stage.in' => 'The stage must be either "store" or "branch".',
            'id.exists' => 'The specified transfer order does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'comment' => 'approval comment',
            'status' => 'approval status',
            'id' => 'transfer order ID',
            'source' => 'approval source',
            'stage' => 'approval stage',
        ];
    }
} 