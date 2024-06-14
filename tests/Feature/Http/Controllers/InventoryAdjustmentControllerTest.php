<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AdjustmentType;
use App\Models\Branch;
use App\Models\InventoryAdjustment;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Reason;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InventoryAdjustmentController
 */
final class InventoryAdjustmentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $inventoryAdjustments = InventoryAdjustment::factory()->count(3)->create();

        $response = $this->get(route('inventory-adjustments.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InventoryAdjustmentController::class,
            'store',
            \App\Http\Requests\InventoryAdjustmentStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $item = Item::factory()->create();
        $adjustment_type = AdjustmentType::factory()->create();
        $date = Carbon::parse($this->faker->dateTime());
        $reason = Reason::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $description = $this->faker->text();
        $item_category = ItemCategory::factory()->create();
        $cost_price = $this->faker->randomFloat(/** float_attributes **/);
        $selling_price = $this->faker->randomFloat(/** float_attributes **/);
        $quantity = $this->faker->word();

        $response = $this->post(route('inventory-adjustments.store'), [
            'item_id' => $item->id,
            'adjustment_type_id' => $adjustment_type->id,
            'date' => $date->toDateTimeString(),
            'reason_id' => $reason->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'description' => $description,
            'item_category_id' => $item_category->id,
            'cost_price' => $cost_price,
            'selling_price' => $selling_price,
            'quantity' => $quantity,
        ]);

        $inventoryAdjustments = InventoryAdjustment::query()
            ->where('item_id', $item->id)
            ->where('adjustment_type_id', $adjustment_type->id)
            ->where('date', $date)
            ->where('reason_id', $reason->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('description', $description)
            ->where('item_category_id', $item_category->id)
            ->where('cost_price', $cost_price)
            ->where('selling_price', $selling_price)
            ->where('quantity', $quantity)
            ->get();
        $this->assertCount(1, $inventoryAdjustments);
        $inventoryAdjustment = $inventoryAdjustments->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $inventoryAdjustment = InventoryAdjustment::factory()->create();

        $response = $this->get(route('inventory-adjustments.show', $inventoryAdjustment));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InventoryAdjustmentController::class,
            'update',
            \App\Http\Requests\InventoryAdjustmentUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $inventoryAdjustment = InventoryAdjustment::factory()->create();
        $item = Item::factory()->create();
        $adjustment_type = AdjustmentType::factory()->create();
        $date = Carbon::parse($this->faker->dateTime());
        $reason = Reason::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $description = $this->faker->text();
        $item_category = ItemCategory::factory()->create();
        $cost_price = $this->faker->randomFloat(/** float_attributes **/);
        $selling_price = $this->faker->randomFloat(/** float_attributes **/);
        $quantity = $this->faker->word();

        $response = $this->put(route('inventory-adjustments.update', $inventoryAdjustment), [
            'item_id' => $item->id,
            'adjustment_type_id' => $adjustment_type->id,
            'date' => $date->toDateTimeString(),
            'reason_id' => $reason->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'description' => $description,
            'item_category_id' => $item_category->id,
            'cost_price' => $cost_price,
            'selling_price' => $selling_price,
            'quantity' => $quantity,
        ]);

        $inventoryAdjustment->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($item->id, $inventoryAdjustment->item_id);
        $this->assertEquals($adjustment_type->id, $inventoryAdjustment->adjustment_type_id);
        $this->assertEquals($date->timestamp, $inventoryAdjustment->date);
        $this->assertEquals($reason->id, $inventoryAdjustment->reason_id);
        $this->assertEquals($branch->id, $inventoryAdjustment->branch_id);
        $this->assertEquals($warehouse->id, $inventoryAdjustment->warehouse_id);
        $this->assertEquals($description, $inventoryAdjustment->description);
        $this->assertEquals($item_category->id, $inventoryAdjustment->item_category_id);
        $this->assertEquals($cost_price, $inventoryAdjustment->cost_price);
        $this->assertEquals($selling_price, $inventoryAdjustment->selling_price);
        $this->assertEquals($quantity, $inventoryAdjustment->quantity);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $inventoryAdjustment = InventoryAdjustment::factory()->create();

        $response = $this->delete(route('inventory-adjustments.destroy', $inventoryAdjustment));

        $response->assertNoContent();

        $this->assertModelMissing($inventoryAdjustment);
    }
}
