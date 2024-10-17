<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'name' => trim($this->surname . ' ' . $this->firstname), 
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'customer_type_id' => $this->customer_type_id,
            'title_id' => $this->title_id,
            
        ];
    }
}
