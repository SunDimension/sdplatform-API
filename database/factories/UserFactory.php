<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Role;
use App\Models\User;

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
            'agency_id' => Agency::factory(),
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'password' => $this->faker->password(),
            'profile_picture' => $this->faker->word(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->word(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->word(),
            'email_verified' => $this->faker->boolean(),
            'phone_verified' => $this->faker->boolean(),
            'kyc_verified' => $this->faker->boolean(),
            'last_login' => $this->faker->dateTime(),
            'remember_token' => $this->faker->uuid(),
        ];
    }
}
