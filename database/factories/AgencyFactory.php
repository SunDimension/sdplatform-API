<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Subscription;

class AgencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Agency::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->word(),
            'registeration_number' => $this->faker->word(),
            'address' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'status' => $this->faker->word(),
            'website' => $this->faker->word(),
            'license_number' => $this->faker->word(),
            'logo' => $this->faker->word(),
            'description' => $this->faker->text(),
            'subscription_id' => Subscription::factory(),
        ];
    }
}
