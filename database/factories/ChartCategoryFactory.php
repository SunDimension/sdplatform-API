<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ChartCategory;
use App\Models\ChartProvider;
use App\Models\Employees,id;

class ChartCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChartCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'chart_provider_id' => ChartProvider::factory(),
            'chart_category' => $this->faker->word(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
