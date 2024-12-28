<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PriceChange;

class PriceChangeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PriceChange::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'details' => '{}',
            'store_id' => $this->faker->word(),
            'branch_id' => $this->faker->word(),
            'change_reason_id' => $this->faker->word(),
            'status' => $this->faker->randomElement(["pending","approved","declined"]),
            'approved_by' => $this->faker->word(),
            'approval_date' => $this->faker->dateTime(),
            'comment' => $this->faker->regexify('[A-Za-z0-9]{1000}'),
            'created_by' => $this->faker->word(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
