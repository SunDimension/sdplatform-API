<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalProcessType;
use App\Models\User;

class ApprovalProcessTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalProcessType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
