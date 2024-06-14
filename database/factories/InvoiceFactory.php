<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\Customerss;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Tax;
use App\Models\Warehouse;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'customer_id' => Customerss::factory(),
            'invoice_number' => $this->faker->word(),
            'order_number' => $this->faker->word(),
            'invoice_date' => $this->faker->dateTime(),
            'item_id' => CreateItem::factory(),
            'rate' => $this->faker->word(),
            'quantity' => $this->faker->word(),
            'discount_id' => Discount::factory(),
            'tax_id' => Tax::factory(),
            'amount' => $this->faker->word(),
        ];
    }
}
