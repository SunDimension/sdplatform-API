<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SubscriptionPlan;

class SubscriptionPlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionPlan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'duration' => $this->faker->numberBetween(-10000, 10000),
            'listing_limit' => $this->faker->numberBetween(-10000, 10000),
            'featured_limit' => $this->faker->numberBetween(-10000, 10000),
            'priority_support' => $this->faker->boolean(),
        ];
    }
}
