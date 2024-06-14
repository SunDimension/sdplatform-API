<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentMode;
use App\Models\SalesReceipt;
use App\Models\Tax;
use App\Models\Warehouse;

class SalesReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesReceipt::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'product_id' => CreateItem::factory(),
            'tax_id' => Tax::factory(),
            'payment_mode_id' => PaymentMode::factory(),
            'discount_id' => Discount::factory(),
            'quantity' => $this->faker->word(),
            'rate' => $this->faker->word(),
            'amount' => $this->faker->word(),
            'receipt_date' => $this->faker->dateTime(),
            'customer_note' => $this->faker->word(),
        ];
    }
}
