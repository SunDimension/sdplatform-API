<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\ReportedListing;
use App\Models\User;

class ReportedListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportedListing::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'reporter_id' => User::factory(),
            'reason' => $this->faker->text(),
            'description' => $this->faker->text(),
            'resolved_by' => User::factory()->create()->resolved_by,
            'resolved_at' => $this->faker->dateTime(),
            'status' => $this->faker->word(),
        ];
    }
}
