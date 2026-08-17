<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\User;

class PropertyViewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyView::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'ip_address' => $this->faker->word(),
            'device' => $this->faker->word(),
            'browser' => $this->faker->word(),
            'country' => $this->faker->country(),
            'viewed_at' => $this->faker->dateTime(),
        ];
    }
}
