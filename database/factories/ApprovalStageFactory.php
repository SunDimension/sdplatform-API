<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalProcessType;
use App\Models\ApprovalStage;
use App\Models\User;

class ApprovalStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'process_type_id' => ApprovalProcessType::factory(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
