<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->supplier_id,
            'supplier_code' => $this->supplier_code,
            'supplier_name' => $this->supplier_name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,

            // Bank relationship
            'bank' => $this->whenLoaded('bank', function () {
                return [
                    'id' => $this->bank->id,
                    'name' => $this->bank->name,
                ];
            }),

            // Bank account details
            'bank_acct_name' => $this->bank_acct_name,
            'bank_acct_num' => $this->bank_acct_num,

            'payment_terms' => $this->payment_terms,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),

            // Sync information
            'sync' => [
                'sync_id' => $this->sync_id,
                'sync_location' => $this->sync_location,
                'sync_version' => $this->sync_version,
                'sync_status' => $this->sync_status,
                'last_sync_at' => $this->last_sync_at?->format('Y-m-d H:i:s'),
                'last_sync_attempt' => $this->last_sync_attempt?->format('Y-m-d H:i:s'),
                'sync_error' => $this->sync_error,
            ],

            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}
