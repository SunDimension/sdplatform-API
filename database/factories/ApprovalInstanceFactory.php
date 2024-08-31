<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalInstance;
use App\Models\ApprovalStage;
use App\Models\ApprovalType;
use App\Models\User;

class ApprovalInstanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalInstance::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'approval_stage_id' => ApprovalStage::factory(),
            'approval_type_id' => ApprovalType::factory(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
