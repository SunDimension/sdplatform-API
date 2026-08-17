<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'agency_id' => $this->agency_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'profile_picture' => $this->profile_picture,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'email_verified' => $this->email_verified,
            'phone_verified' => $this->phone_verified,
            'kyc_verified' => $this->kyc_verified,
            'last_login' => $this->last_login,
        ];
    }
}
