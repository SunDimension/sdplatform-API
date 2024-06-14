<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Title;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_type_id' => CustomerType::factory(),
            'title_id' => Title::factory(),
            'surname' => $this->faker->word(),
            'firstname' => $this->faker->firstName(),
            'middlename' => $this->faker->word(),
            'phone_number' => $this->faker->phoneNumber(),
            'fullname' => $this->faker->word(),
        ];
    }
}
