<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AccountGroup;
use App\Models\AccountType;
use App\Models\User;

class AccountTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'account_group_id' => AccountGroup::factory(),
            'name' => $this->faker->name(),
            'code' => $this->faker->word(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
