<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreateItem;
use App\Models\ItemCategory;
use App\Models\PurchaseOrderDetail;
use App\Models\Unit;

class PurchaseOrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseOrderDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_category_id' => ItemCategory::factory(),
            'purchase_order_id' => $this->faker->word(),
            'item_id' => CreateItem::factory(),
            'unit_price' => $this->faker->word(),
            'quantity' => $this->faker->word(),
            'unit_id' => Unit::factory(),
        ];
    }
}
