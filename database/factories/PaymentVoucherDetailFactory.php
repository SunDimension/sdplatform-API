<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreateItem;
use App\Models\PaymentVoucherDetail;

class PaymentVoucherDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentVoucherDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'Expense_account_id' => $this->faker->word(),
            'amount' => $this->faker->word(),
            'quantity' => $this->faker->word(),
            'item_id' => CreateItem::factory(),
        ];
    }
}
