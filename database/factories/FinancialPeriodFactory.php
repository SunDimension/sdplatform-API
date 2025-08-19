<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FinancialPeriod;
use App\Models\FinancialQuarter;
use App\Models\FinancialYear;
use App\Models\User;
use Carbon\Carbon;

class FinancialPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialPeriod::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', '+1 year');
        $endDate = Carbon::parse($startDate)->endOfMonth();
        
        return [
            'name' => Carbon::parse($startDate)->format('M Y'),
            'date_from' => $startDate,
            'date_to' => $endDate,
            'is_active' => $this->faker->boolean(20), // 20% chance of being active
            'financial_year_id' => FinancialYear::factory(),
            'financial_quarter_id' => FinancialQuarter::factory(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => null,
        ];
    }

    /**
     * Create a financial period for a specific month and year
     */
    public function forMonth(int $month, int $year): static
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        return $this->state(function (array $attributes) use ($startDate, $endDate, $month, $year) {
            return [
                'name' => $startDate->format('M Y'),
                'date_from' => $startDate,
                'date_to' => $endDate,
            ];
        });
    }

    /**
     * Create an active financial period
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Create an inactive financial period
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
