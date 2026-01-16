<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LedgerAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'account_id' => $this->account_id,
            'account_type_id' => $this->account_type_id,
            // 'account_name' => $this->branch ? $this->branch->name : null,
            'account_name' => $this->account_name,
            'account_code' => $this->account_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
