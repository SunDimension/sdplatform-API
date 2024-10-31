<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreateItem;
use App\Models\StoreTransferItem;
use App\Models\StoreTransferOrder;
use App\Models\User;

class StoreTransferItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StoreTransferItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'transfer_order_id' => StoreTransferOrder::factory(),
            'quantity' => $this->faker->word(),
            'unit_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'product_id' => CreateItem::factory(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
