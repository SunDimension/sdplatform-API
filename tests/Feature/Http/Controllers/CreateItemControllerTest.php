<?php

namespace Tests\Feature\Http\Controllers;

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreateItemController
 */
final class CreateItemControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $createItems = CreateItem::factory()->count(3)->create();

        $response = $this->get(route('create-items.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreateItemController::class,
            'store',
            \App\Http\Requests\CreateItemStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $item_category = ItemCategory::factory()->create();
        $item_type = ItemType::factory()->create();
        $description = $this->faker->text();
        $batch_number = $this->faker->word();
        $unit = Unit::factory()->create();
        $brand = Brand::factory()->create();
        $cost_price = $this->faker->randomFloat(/** float_attributes **/);
        $selling_price = $this->faker->randomFloat(/** float_attributes **/);
        $reorder_level = $this->faker->word();
        $dimension = Dimension::factory()->create();
        $weight = Weight::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $vendor = Vendor::factory()->create();
        $image_url = $this->faker->word();
        $barcode = $this->faker->word();

        $response = $this->post(route('create-items.store'), [
            'name' => $name,
            'item_category_id' => $item_category->id,
            'item_type_id' => $item_type->id,
            'description' => $description,
            'batch_number' => $batch_number,
            'unit_id' => $unit->id,
            'brand_id' => $brand->id,
            'cost_price' => $cost_price,
            'selling_price' => $selling_price,
            'reorder_level' => $reorder_level,
            'dimension_id' => $dimension->id,
            'weight_id' => $weight->id,
            'branch_id' => $branch->id,
            'warehouse' => $warehouse->id,
            'vendor_id' => $vendor->id,
            'image_url' => $image_url,
            'barcode' => $barcode,
        ]);

        $createItems = CreateItem::query()
            ->where('name', $name)
            ->where('item_category_id', $item_category->id)
            ->where('item_type_id', $item_type->id)
            ->where('description', $description)
            ->where('batch_number', $batch_number)
            ->where('unit_id', $unit->id)
            ->where('brand_id', $brand->id)
            ->where('cost_price', $cost_price)
            ->where('selling_price', $selling_price)
            ->where('reorder_level', $reorder_level)
            ->where('dimension_id', $dimension->id)
            ->where('weight_id', $weight->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse', $warehouse->id)
            ->where('vendor_id', $vendor->id)
            ->where('image_url', $image_url)
            ->where('barcode', $barcode)
            ->get();
        $this->assertCount(1, $createItems);
        $createItem = $createItems->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $createItem = CreateItem::factory()->create();

        $response = $this->get(route('create-items.show', $createItem));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreateItemController::class,
            'update',
            \App\Http\Requests\CreateItemUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $createItem = CreateItem::factory()->create();
        $name = $this->faker->name();
        $item_category = ItemCategory::factory()->create();
        $item_type = ItemType::factory()->create();
        $description = $this->faker->text();
        $batch_number = $this->faker->word();
        $unit = Unit::factory()->create();
        $brand = Brand::factory()->create();
        $cost_price = $this->faker->randomFloat(/** float_attributes **/);
        $selling_price = $this->faker->randomFloat(/** float_attributes **/);
        $reorder_level = $this->faker->word();
        $dimension = Dimension::factory()->create();
        $weight = Weight::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $vendor = Vendor::factory()->create();
        $image_url = $this->faker->word();
        $barcode = $this->faker->word();

        $response = $this->put(route('create-items.update', $createItem), [
            'name' => $name,
            'item_category_id' => $item_category->id,
            'item_type_id' => $item_type->id,
            'description' => $description,
            'batch_number' => $batch_number,
            'unit_id' => $unit->id,
            'brand_id' => $brand->id,
            'cost_price' => $cost_price,
            'selling_price' => $selling_price,
            'reorder_level' => $reorder_level,
            'dimension_id' => $dimension->id,
            'weight_id' => $weight->id,
            'branch_id' => $branch->id,
            'warehouse' => $warehouse->id,
            'vendor_id' => $vendor->id,
            'image_url' => $image_url,
            'barcode' => $barcode,
        ]);

        $createItem->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $createItem->name);
        $this->assertEquals($item_category->id, $createItem->item_category_id);
        $this->assertEquals($item_type->id, $createItem->item_type_id);
        $this->assertEquals($description, $createItem->description);
        $this->assertEquals($batch_number, $createItem->batch_number);
        $this->assertEquals($unit->id, $createItem->unit_id);
        $this->assertEquals($brand->id, $createItem->brand_id);
        $this->assertEquals($cost_price, $createItem->cost_price);
        $this->assertEquals($selling_price, $createItem->selling_price);
        $this->assertEquals($reorder_level, $createItem->reorder_level);
        $this->assertEquals($dimension->id, $createItem->dimension_id);
        $this->assertEquals($weight->id, $createItem->weight_id);
        $this->assertEquals($branch->id, $createItem->branch_id);
        $this->assertEquals($warehouse->id, $createItem->warehouse);
        $this->assertEquals($vendor->id, $createItem->vendor_id);
        $this->assertEquals($image_url, $createItem->image_url);
        $this->assertEquals($barcode, $createItem->barcode);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $createItem = CreateItem::factory()->create();

        $response = $this->delete(route('create-items.destroy', $createItem));

        $response->assertNoContent();

        $this->assertModelMissing($createItem);
    }
}
