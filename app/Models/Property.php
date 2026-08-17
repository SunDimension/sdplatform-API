<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id',
        'owner_id',
        'agent_id',
        'property_type_id',
        'category_id',
        'status_id',
        'purpose',
        'description',
        'price',
        'title',
        'currency',
        'negotiable',
        'bedrooms',
        'bathrooms',
        'toilets',
        'parking_spaces',
        'kitchen',
        'living_rooms',
        'land_size',
        'building_size',
        'year_built',
        'floors',
        'furnished',
        'serviced',
        'pet_friendly',
        'minimum_rent_period',
        'available_from',
        'longitude',
        'latitude',
        'country_id',
        'state_id',
        'city_id',
        'area_id',
        'street_address',
        'postal_code',
        'featured',
        'premium',
        'verified',
        'views',
        'likes',
        'published_at',
        'expiry_date',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'negotiable' => 'boolean',
        'land_size' => 'float',
        'building_size' => 'float',
        'furnished' => 'boolean',
        'serviced' => 'boolean',
        'pet_friendly' => 'boolean',
        'available_from' => 'date',
        'longitude' => 'float',
        'latitude' => 'float',
        'featured' => 'boolean',
        'premium' => 'boolean',
        'verified' => 'boolean',
        'published_at' => 'timestamp',
        'expiry_date' => 'timestamp',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PropertyStatus::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
