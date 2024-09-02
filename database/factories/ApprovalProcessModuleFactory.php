<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalProcessModule;
use App\Models\User;

class ApprovalProcessModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalProcessModule::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'max_approval_count' => $this->faker->word(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
