<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ChartCategory;
use App\Models\ChartType;
use App\Models\User;

class ChartTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChartType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'chart_category_id' => ChartCategory::factory(),
            'chart_type' => $this->faker->word(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
