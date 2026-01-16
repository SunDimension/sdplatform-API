<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRecievedUpdateRequest extends FormRequest
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
            'po_id' => ['required', 'string', 'exists:purchase_orders,po_id'],
            // 'pr_id' => ['nullable', 'string', 'exists:items,id'],
            // 'order_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'received_date' => ['required', 'date'],
            'recieved_by' => ['required', 'string', 'exists:users,id'],
            // 'unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
