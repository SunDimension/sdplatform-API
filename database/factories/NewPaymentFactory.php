<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\NewPayment;
use App\Models\PaymentMode;
use App\Models\Vendor;
use App\Models\Warehouse;

class NewPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewPayment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'payment_amount' => $this->faker->word(),
            'payment_mode_id' => PaymentMode::factory(),
            'description' => $this->faker->text(),
        ];
    }
}
