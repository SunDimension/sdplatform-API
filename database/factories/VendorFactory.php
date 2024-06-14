<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Bank;
use App\Models\Vendor;
use App\Models\VendorType;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->word(),
            'contact_title' => $this->faker->word(),
            'contact_designation' => $this->faker->word(),
            'contact_surname' => $this->faker->word(),
            'contact_firstname' => $this->faker->word(),
            'contact_middlename' => $this->faker->word(),
            'contact_fullname' => $this->faker->word(),
            'vendor_type_id' => VendorType::factory(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'image_url' => $this->faker->word(),
            'tin' => $this->faker->word(),
            'bank_id' => Bank::factory(),
            'account_number' => $this->faker->word(),
        ];
    }
}
