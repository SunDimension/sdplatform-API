<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalProcessFlow;
use App\Models\ApprovalProcessModule;
use App\Models\ApprovalStage;
use App\Models\User;

class ApprovalProcessFlowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalProcessFlow::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sequence_no' => $this->faker->word(),
            'process_module_id' => ApprovalProcessModule::factory(),
            'approval_stage_id' => ApprovalStage::factory(),
            'status_id' => $this->faker->word(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
