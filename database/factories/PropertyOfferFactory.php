<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyOffer;
use App\Models\User;

class PropertyOfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyOffer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'buyer_id' => User::factory(),
            'offer_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->word(),
            'message' => $this->faker->text(),
            'accepted_at' => $this->faker->dateTime(),
            'rejected_at' => $this->faker->dateTime(),
        ];
    }
}
