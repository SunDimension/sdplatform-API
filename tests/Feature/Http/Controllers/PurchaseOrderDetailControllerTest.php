<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\PurchaseOrderDetail;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PurchaseOrderDetailController
 */
final class PurchaseOrderDetailControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $purchaseOrderDetails = PurchaseOrderDetail::factory()->count(3)->create();

        $response = $this->get(route('purchase-order-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseOrderDetailController::class,
            'store',
            \App\Http\Requests\PurchaseOrderDetailStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $item_category = ItemCategory::factory()->create();
        $purchase_order_id = $this->faker->word();
        $item = Item::factory()->create();
        $unit_price = $this->faker->word();
        $quantity = $this->faker->word();
        $unit = Unit::factory()->create();

        $response = $this->post(route('purchase-order-details.store'), [
            'item_category_id' => $item_category->id,
            'purchase_order_id' => $purchase_order_id,
            'item_id' => $item->id,
            'unit_price' => $unit_price,
            'quantity' => $quantity,
            'unit_id' => $unit->id,
        ]);

        $purchaseOrderDetails = PurchaseOrderDetail::query()
            ->where('item_category_id', $item_category->id)
            ->where('purchase_order_id', $purchase_order_id)
            ->where('item_id', $item->id)
            ->where('unit_price', $unit_price)
            ->where('quantity', $quantity)
            ->where('unit_id', $unit->id)
            ->get();
        $this->assertCount(1, $purchaseOrderDetails);
        $purchaseOrderDetail = $purchaseOrderDetails->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $purchaseOrderDetail = PurchaseOrderDetail::factory()->create();

        $response = $this->get(route('purchase-order-details.show', $purchaseOrderDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseOrderDetailController::class,
            'update',
            \App\Http\Requests\PurchaseOrderDetailUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $purchaseOrderDetail = PurchaseOrderDetail::factory()->create();
        $item_category = ItemCategory::factory()->create();
        $purchase_order_id = $this->faker->word();
        $item = Item::factory()->create();
        $unit_price = $this->faker->word();
        $quantity = $this->faker->word();
        $unit = Unit::factory()->create();

        $response = $this->put(route('purchase-order-details.update', $purchaseOrderDetail), [
            'item_category_id' => $item_category->id,
            'purchase_order_id' => $purchase_order_id,
            'item_id' => $item->id,
            'unit_price' => $unit_price,
            'quantity' => $quantity,
            'unit_id' => $unit->id,
        ]);

        $purchaseOrderDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($item_category->id, $purchaseOrderDetail->item_category_id);
        $this->assertEquals($purchase_order_id, $purchaseOrderDetail->purchase_order_id);
        $this->assertEquals($item->id, $purchaseOrderDetail->item_id);
        $this->assertEquals($unit_price, $purchaseOrderDetail->unit_price);
        $this->assertEquals($quantity, $purchaseOrderDetail->quantity);
        $this->assertEquals($unit->id, $purchaseOrderDetail->unit_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $purchaseOrderDetail = PurchaseOrderDetail::factory()->create();

        $response = $this->delete(route('purchase-order-details.destroy', $purchaseOrderDetail));

        $response->assertNoContent();

        $this->assertModelMissing($purchaseOrderDetail);
    }
}
