<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_type_id' => $this->customer_type_id,
            'title_id' => $this->title_id,
            'surname' => $this->surname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'phone_number' => $this->phone_number,
            'fullname' => $this->fullname,
        ];
    }
}
