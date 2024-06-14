<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\CreateItem;
use App\Models\Dimension;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\Weight;

class CreateItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreateItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'item_category_id' => ItemCategory::factory(),
            'item_type_id' => ItemType::factory(),
            'description' => $this->faker->text(),
            'batch_number' => $this->faker->word(),
            'unit_id' => Unit::factory(),
            'brand_id' => Brand::factory(),
            'cost_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'selling_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'reorder_level' => $this->faker->word(),
            'dimension_id' => Dimension::factory(),
            'weight_id' => Weight::factory(),
            'branch_id' => Branch::factory(),
            'warehouse' => Warehouse::factory()->create()->warehouse,
            'vendor_id' => Vendor::factory(),
            'image_url' => $this->faker->word(),
            'barcode' => $this->faker->word(),
        ];
    }
}
