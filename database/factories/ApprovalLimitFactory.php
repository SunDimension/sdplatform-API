<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalLimit;
use App\Models\User;

class ApprovalLimitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalLimit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'user_id' => User::factory(),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
