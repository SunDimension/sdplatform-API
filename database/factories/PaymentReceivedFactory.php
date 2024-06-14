<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Models\PaymentReceived;

class PaymentReceivedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentReceived::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'amount_received' => $this->faker->word(),
            'bank_charges' => $this->faker->randomFloat(0, 0, 9999999999.),
            'payment_number' => $this->faker->word(),
            'deposit_bank_id' => Bank::factory(),
            'payment_mode_id' => PaymentMode::factory(),
            'invoice_number' => $this->faker->word(),
        ];
    }
}
