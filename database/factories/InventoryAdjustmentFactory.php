<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AdjustmentType;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Models\InventoryAdjustment;
use App\Models\ItemCategory;
use App\Models\Reason;
use App\Models\Warehouse;

class InventoryAdjustmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InventoryAdjustment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => CreateItem::factory(),
            'adjustment_type_id' => AdjustmentType::factory(),
            'date' => $this->faker->dateTime(),
            'reason_id' => Reason::factory(),
            'branch_id' => Branch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'description' => $this->faker->text(),
            'item_category_id' => ItemCategory::factory(),
            'cost_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'selling_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'quantity' => $this->faker->word(),
        ];
    }
}
