<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FinancialQuarter;
use App\Models\FinancialYear;
use App\Models\User;

class FinancialQuarterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialQuarter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'date_from' => $this->faker->date(),
            'date_to' => $this->faker->date(),
            'is_active' => $this->faker->boolean(),
            'financial_year_id' => FinancialYear::factory(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
