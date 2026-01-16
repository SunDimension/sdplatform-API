<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRecievedStoreRequest extends FormRequest
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
            'recieved_date' => ['nullable', 'date'],
            'recieved_by' => ['required', 'string', 'exists:users,id'],
            // 'unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
