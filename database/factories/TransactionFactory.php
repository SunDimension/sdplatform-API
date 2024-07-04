<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Accounts,id;
use App\Models\Employees,id;
use App\Models\FinancialPeriod;
use App\Models\Transaction;
use App\Models\Warehouse;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'financial_period_id' => FinancialPeriod::factory(),
            'transaction_date' => $this->faker->date(),
            'transcode' => $this->faker->word(),
            'transtype' => $this->faker->word(),
            'naration' => $this->faker->word(),
            'debit' => $this->faker->randomFloat(0, 0, 9999999999.),
            'credit' => $this->faker->randomFloat(0, 0, 9999999999.),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'warehouse_id' => Warehouse::factory(),
            'account_no' => $this->faker->word(),
            'account_id' => Accounts,id::factory(),
            'created_by' => Employees,id::factory()->create()->created_by,
            'modified_by' => Employees,id::factory()->create()->modified_by,
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
