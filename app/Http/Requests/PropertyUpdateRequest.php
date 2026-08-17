<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'agency_id' => ['nullable'],
            'owner_id' => ['required'],
            'agent_id' => ['nullable'],
            'property_type_id' => ['required'],
            'category_id' => ['required'],
            'status_id' => ['required'],
            'purpose' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'title' => ['required', 'string'],
            'currency' => ['required', 'string'],
            'negotiable' => ['required'],
            'bedrooms' => ['nullable', 'integer'],
            'bathrooms' => ['nullable', 'integer'],
            'toilets' => ['nullable', 'integer'],
            'parking_spaces' => ['nullable', 'integer'],
            'kitchen' => ['nullable', 'integer'],
            'living_rooms' => ['nullable', 'integer'],
            'land_size' => ['nullable', 'numeric'],
            'building_size' => ['nullable', 'numeric'],
            'year_built' => ['nullable', 'integer'],
            'floors' => ['nullable', 'integer'],
            'furnished' => ['required'],
            'serviced' => ['required'],
            'pet_friendly' => ['required'],
            'minimum_rent_period' => ['nullable', 'string'],
            'available_from' => ['nullable', 'date'],
            'longitude' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'country_id' => ['required'],
            'state_id' => ['required'],
            'city_id' => ['required'],
            'area_id' => ['nullable'],
            'street_address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'featured' => ['required'],
            'premium' => ['required'],
            'verified' => ['required'],
            'views' => ['required', 'integer'],
            'likes' => ['required', 'integer'],
            'published_at' => ['nullable'],
            'expiry_date' => ['nullable'],
        ];
    }
}
