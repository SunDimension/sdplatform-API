<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'registeration_number' => $this->registeration_number,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'website' => $this->website,
            'license_number' => $this->license_number,
            'logo' => $this->logo,
            'description' => $this->description,
            'subscription_id' => $this->subscription_id,
        ];
    }
}
