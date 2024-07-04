<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Chart;
use App\Models\Employees,id;

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
            'chart_type_id' => $this->faker->word(),
            'chart_category_id' => $this->faker->word(),
            'sql_query' => $this->faker->text(),
            'is_active' => $this->faker->word(),
            'module_id' => $this->faker->word(),
            'filterColumn' => $this->faker->word(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
