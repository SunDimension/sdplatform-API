<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\MortgageCalculation;
use App\Models\Property;
use App\Models\User;

class MortgageCalculationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MortgageCalculation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'loan_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'interest_rate' => $this->faker->randomFloat(0, 0, 9999999999.),
            'loan_term' => $this->faker->numberBetween(-10000, 10000),
            'monthly_payment' => $this->faker->randomFloat(0, 0, 9999999999.),
            'total_payment' => $this->faker->randomFloat(0, 0, 9999999999.),
        ];
    }
}
