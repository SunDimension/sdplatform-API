<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_group_id' => $this->account_group_id,
            'name' => $this->name,
            'code' => $this->code,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'deleted_by' => $this->deleted_by,
            'accountSubtypes' => AccountSubtypeCollection::make($this->whenLoaded('accountSubtypes')),
        ];
    }
}
