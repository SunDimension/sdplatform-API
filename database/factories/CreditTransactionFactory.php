<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesReceipt;
use App\Models\User;

class CreditTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditTransaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'sales_order_id' => SalesOrder::factory(),
            'sales_receipt_id' => SalesReceipt::factory(),
            'amount' => $this->faker->word(),
            'credit_limit' => $this->faker->word(),
            'credit_balance_before' => $this->faker->word(),
            'type' => $this->faker->randomElement(["('credit'",""]),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'deleted_by' => User::factory()->create()->deleted_by,
        ];
    }
}
