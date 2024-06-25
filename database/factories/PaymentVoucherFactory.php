<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\ExpenseAccountId;
use App\Models\PaymentMode;
use App\Models\PaymentVoucher;
use App\Models\Tax;
use App\Models\Vendor;
use App\Models\Warehouse;

class PaymentVoucherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentVoucher::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => CreateItem::factory(),
            'expense_date' => $this->faker->dateTime(),
            'amount' => $this->faker->word(),
            'description' => $this->faker->text(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'tax_id' => Tax::factory(),
            'vendor_id' => Vendor::factory(),
            'payment_mode_id' => PaymentMode::factory(),
            'expense_account_id' => ExpenseAccountId::factory(),
        ];
    }
}
