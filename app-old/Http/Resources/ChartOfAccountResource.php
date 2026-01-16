<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartOfAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'account_id' => $this->account_id,
            'account_name' => $this->account_name,
            'account_code' => $this->account_code,
            'account_type' => $this->accountType ? $this->accountType->account_type : null,
            'account_type_id' => $this->account_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
