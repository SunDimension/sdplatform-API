<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ChartCard;
use App\Models\Employees,id;

class ChartCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChartCard::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'card_title' => $this->faker->word(),
            'card_size' => $this->faker->word(),
            'is_active' => $this->faker->word(),
            'sql_query' => $this->faker->text(),
            'module_id' => $this->faker->word(),
            'submodule_id' => $this->faker->word(),
            'sequence' => $this->faker->word(),
            'color' => $this->faker->word(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
