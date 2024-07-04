<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Chart;
use App\Models\ChartCategory;
use App\Models\ChartType;
use App\Models\User;

class ChartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chart::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'chart_title' => $this->faker->word(),
            'chart_type_id' => ChartType::factory(),
            'chart_category_id' => ChartCategory::factory(),
            'sql_query' => $this->faker->text(),
            'is_active' => $this->faker->word(),
            'module_id' => $this->faker->word(),
            'filterColumn' => $this->faker->word(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
