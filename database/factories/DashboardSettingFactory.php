<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Chart;
use App\Models\ChartCategory;
use App\Models\ChartType;
use App\Models\DashboardSetting;
use App\Models\Employees,id;
use App\Models\Module;

class DashboardSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DashboardSetting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'chart_id' => Chart::factory(),
            'module_id' => Module::factory(),
            'chart_type_id' => ChartType::factory(),
            'chart_category_id' => ChartCategory::factory(),
            'chart_title' => $this->faker->word(),
            'is_active' => $this->faker->word(),
            'order_by' => $this->faker->word(),
            'is_group' => $this->faker->word(),
            'submodule_Id' => $this->faker->word(),
            'add_condition' => $this->faker->word(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
