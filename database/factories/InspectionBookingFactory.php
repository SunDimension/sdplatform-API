<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\InspectionBooking;
use App\Models\Property;
use App\Models\User;

class InspectionBookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InspectionBooking::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'scheduled_at' => $this->faker->dateTime(),
            'schedule_time' => $this->faker->word(),
            'remarks' => $this->faker->text(),
            'status' => $this->faker->word(),
        ];
    }
}
