<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreateItem;
use App\Models\TransferOrder;
use App\Models\Warehouse;

class TransferOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransferOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'transfer_order_number' => $this->faker->word(),
            'transfer_date' => $this->faker->dateTime(),
            'transfer_reason' => $this->faker->word(),
            'source_warehouse_id' => Warehouse::factory(),
            'destination_warehouse_id' => Warehouse::factory(),
            'image_url' => $this->faker->word(),
            'transfer_quantity' => $this->faker->word(),
            'item_id' => CreateItem::factory(),
        ];
    }
}
