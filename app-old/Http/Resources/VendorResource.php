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
            'title' => $this->title,
            'designation' => $this->designation,
            'contact_surname' => $this->contact_surname,
            'contact_firstname' => $this->contact_firstname,
            'contact_middlename' => $this->contact_middlename,
            'vendor_type' => $this->vendor_type,
            'service_type' => $this->service_type,
            'contact_phone_number' => $this->contact_phone_number,
            'contact_email' => $this->contact_email,
            'image_url' => $this->image_url,
            'tin' => $this->tin,
            'bank' => $this->bank,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
        ];
    }
}
