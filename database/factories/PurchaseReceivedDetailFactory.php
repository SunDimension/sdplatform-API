<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreateItem;
use App\Models\ItemCategory;
use App\Models\NewPurchaseReceived;
use App\Models\PurchaseReceivedDetail;
use App\Models\Unit;

class PurchaseReceivedDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseReceivedDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'new_purchased_received_id' => NewPurchaseReceived::factory(),
            'item_category_id' => ItemCategory::factory(),
            'item_id' => CreateItem::factory(),
            'unit_price' => $this->faker->word(),
            'quantity' => $this->faker->word(),
            'unit_id' => Unit::factory(),
        ];
    }
}
