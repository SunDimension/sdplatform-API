<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AccountSubtype;
use App\Models\AccountType;
use App\Models\Employees,id;

class AccountSubtypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountSubtype::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'account_type_id' => AccountType::factory(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
