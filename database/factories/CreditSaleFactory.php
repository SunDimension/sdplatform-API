<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\CreditLimit;
use App\Models\CreditSale;
use App\Models\Customerss;
use App\Models\Warehouse;

class CreditSaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditSale::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customerss::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'product_id' => CreateItem::factory(),
            'credit_limit' => CreditLimit::factory()->create()->credit_limit,
            'credit_amount' => $this->faker->word(),
            'credit_balance' => $this->faker->word(),
        ];
    }
}
