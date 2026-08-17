<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\User;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subscription_id' => Subscription::factory(),
            'property_id' => Property::factory(),
            'currency' => $this->faker->word(),
            'gateway' => $this->faker->word(),
            'transaction_reference' => $this->faker->word(),
            'paid_at' => $this->faker->dateTime(),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'payment_status' => $this->faker->word(),
        ];
    }
}
