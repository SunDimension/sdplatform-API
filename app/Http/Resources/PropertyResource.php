<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'agency_id' => $this->agency_id,
            'owner_id' => $this->owner_id,
            'agent_id' => $this->agent_id,
            'property_type_id' => $this->property_type_id,
            'category_id' => $this->category_id,
            'status_id' => $this->status_id,
            'purpose' => $this->purpose,
            'description' => $this->description,
            'price' => $this->price,
            'title' => $this->title,
            'currency' => $this->currency,
            'negotiable' => $this->negotiable,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'toilets' => $this->toilets,
            'parking_spaces' => $this->parking_spaces,
            'kitchen' => $this->kitchen,
            'living_rooms' => $this->living_rooms,
            'land_size' => $this->land_size,
            'building_size' => $this->building_size,
            'year_built' => $this->year_built,
            'floors' => $this->floors,
            'furnished' => $this->furnished,
            'serviced' => $this->serviced,
            'pet_friendly' => $this->pet_friendly,
            'minimum_rent_period' => $this->minimum_rent_period,
            'available_from' => $this->available_from,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'street_address' => $this->street_address,
            'postal_code' => $this->postal_code,
            'featured' => $this->featured,
            'premium' => $this->premium,
            'verified' => $this->verified,
            'views' => $this->views,
            'likes' => $this->likes,
            'published_at' => $this->published_at,
            'expiry_date' => $this->expiry_date,
        ];
    }
}
