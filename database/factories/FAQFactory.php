<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FAQ;

class FAQFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FAQ::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->word(),
            'answer' => $this->faker->text(),
            'status' => $this->faker->word(),
        ];
    }
}
