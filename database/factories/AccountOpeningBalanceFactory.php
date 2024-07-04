<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use App\Models\User;
use App\Models\Warehouse;

class AccountOpeningBalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountOpeningBalance::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'financial_year_id' => FinancialYear::factory(),
            'financial_period_id' => FinancialPeriod::factory(),
            'debit' => $this->faker->randomFloat(0, 0, 9999999999.),
            'credit' => $this->faker->randomFloat(0, 0, 9999999999.),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'warehouse_id' => Warehouse::factory(),
            'account_no' => $this->faker->word(),
            'account_id' => Account::factory(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
