<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\Customerss;
use App\Models\Discount;
use App\Models\PaymentMode;
use App\Models\Sale;
use App\Models\Warehouse;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customerss::factory(),
            'product_id' => CreateItem::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => $this->faker->randomFloat(0, 0, 9999999999.),
            'price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'discount_id' => Discount::factory(),
            'discount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'sales_order_number' => $this->faker->word(),
            'total_amount' => $this->faker->word(),
            'amount_paid' => $this->faker->randomFloat(0, 0, 9999999999.),
            'balance_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'payment_mode' => PaymentMode::factory()->create()->payment_mode,
        ];
    }
}
