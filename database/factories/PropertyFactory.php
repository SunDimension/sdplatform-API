<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'owner_id' => User::factory(),
            'agent_id' => User::factory(),
            'property_type_id' => PropertyType::factory(),
            'category_id' => PropertyCategory::factory(),
            'status_id' => PropertyStatus::factory(),
            'purpose' => $this->faker->word(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'title' => $this->faker->sentence(4),
            'currency' => $this->faker->word(),
            'negotiable' => $this->faker->boolean(),
            'bedrooms' => $this->faker->numberBetween(-10000, 10000),
            'bathrooms' => $this->faker->numberBetween(-10000, 10000),
            'toilets' => $this->faker->numberBetween(-10000, 10000),
            'parking_spaces' => $this->faker->numberBetween(-10000, 10000),
            'kitchen' => $this->faker->numberBetween(-10000, 10000),
            'living_rooms' => $this->faker->numberBetween(-10000, 10000),
            'land_size' => $this->faker->randomFloat(0, 0, 9999999999.),
            'building_size' => $this->faker->randomFloat(0, 0, 9999999999.),
            'year_built' => $this->faker->numberBetween(-10000, 10000),
            'floors' => $this->faker->numberBetween(-10000, 10000),
            'furnished' => $this->faker->boolean(),
            'serviced' => $this->faker->boolean(),
            'pet_friendly' => $this->faker->boolean(),
            'minimum_rent_period' => $this->faker->word(),
            'available_from' => $this->faker->date(),
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude(),
            'country_id' => Country::factory(),
            'state_id' => State::factory(),
            'city_id' => City::factory(),
            'area_id' => Area::factory(),
            'street_address' => $this->faker->word(),
            'postal_code' => $this->faker->postcode(),
            'featured' => $this->faker->boolean(),
            'premium' => $this->faker->boolean(),
            'verified' => $this->faker->boolean(),
            'views' => $this->faker->numberBetween(-10000, 10000),
            'likes' => $this->faker->numberBetween(-10000, 10000),
            'published_at' => $this->faker->dateTime(),
            'expiry_date' => $this->faker->dateTime(),
        ];
    }
}
