<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Models\Warehouse;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'role_id' => Role::factory(),
            'name' => $this->faker->userName(),
            'email' => $this->faker->word(),
            'password' => $this->faker->password(),
            'status_id' => Status::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
        ];
    }
}
