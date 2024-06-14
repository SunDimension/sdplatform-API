<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\ItemCategory;
use App\Models\NewPurchaseOrder;
use App\Models\PaymentMode;
use App\Models\PaymentType;
use App\Models\Vendor;

class NewPurchaseOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewPurchaseOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_category_id' => ItemCategory::factory(),
            'item_id' => CreateItem::factory(),
            'vendor_id' => Vendor::factory(),
            'branch_id' => Branch::factory(),
            'payment_mode_id' => PaymentMode::factory(),
            'purchase_order_number' => $this->faker->word(),
            'purchase_amount' => $this->faker->word(),
            'purchase_date' => $this->faker->dateTime(),
            'expected_delivery_date' => $this->faker->date(),
            'payment_type_id' => PaymentType::factory(),
        ];
    }
}
