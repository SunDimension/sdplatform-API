<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'contact_title' => $this->contact_title,
            'contact_designation' => $this->contact_designation,
            'contact_surname' => $this->contact_surname,
            'contact_firstname' => $this->contact_firstname,
            'contact_middlename' => $this->contact_middlename,
            'contact_fullname' => $this->contact_fullname,
            'vendor_type_id' => $this->vendor_type_id,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'image_url' => $this->image_url,
            'tin' => $this->tin,
            'bank_id' => $this->bank_id,
            'account_number' => $this->account_number,
        ];
    }
}
