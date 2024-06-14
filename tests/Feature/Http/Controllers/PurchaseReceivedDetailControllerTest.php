<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\NewPurchasedReceived;
use App\Models\PurchaseReceivedDetail;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PurchaseReceivedDetailController
 */
final class PurchaseReceivedDetailControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $purchaseReceivedDetails = PurchaseReceivedDetail::factory()->count(3)->create();

        $response = $this->get(route('purchase-received-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseReceivedDetailController::class,
            'store',
            \App\Http\Requests\PurchaseReceivedDetailStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $new_purchased_received = NewPurchasedReceived::factory()->create();
        $item_category = ItemCategory::factory()->create();
        $item = Item::factory()->create();
        $unit_price = $this->faker->word();
        $quantity = $this->faker->word();
        $unit = Unit::factory()->create();

        $response = $this->post(route('purchase-received-details.store'), [
            'new_purchased_received_id' => $new_purchased_received->id,
            'item_category_id' => $item_category->id,
            'item_id' => $item->id,
            'unit_price' => $unit_price,
            'quantity' => $quantity,
            'unit_id' => $unit->id,
        ]);

        $purchaseReceivedDetails = PurchaseReceivedDetail::query()
            ->where('new_purchased_received_id', $new_purchased_received->id)
            ->where('item_category_id', $item_category->id)
            ->where('item_id', $item->id)
            ->where('unit_price', $unit_price)
            ->where('quantity', $quantity)
            ->where('unit_id', $unit->id)
            ->get();
        $this->assertCount(1, $purchaseReceivedDetails);
        $purchaseReceivedDetail = $purchaseReceivedDetails->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $purchaseReceivedDetail = PurchaseReceivedDetail::factory()->create();

        $response = $this->get(route('purchase-received-details.show', $purchaseReceivedDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseReceivedDetailController::class,
            'update',
            \App\Http\Requests\PurchaseReceivedDetailUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $purchaseReceivedDetail = PurchaseReceivedDetail::factory()->create();
        $new_purchased_received = NewPurchasedReceived::factory()->create();
        $item_category = ItemCategory::factory()->create();
        $item = Item::factory()->create();
        $unit_price = $this->faker->word();
        $quantity = $this->faker->word();
        $unit = Unit::factory()->create();

        $response = $this->put(route('purchase-received-details.update', $purchaseReceivedDetail), [
            'new_purchased_received_id' => $new_purchased_received->id,
            'item_category_id' => $item_category->id,
            'item_id' => $item->id,
            'unit_price' => $unit_price,
            'quantity' => $quantity,
            'unit_id' => $unit->id,
        ]);

        $purchaseReceivedDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($new_purchased_received->id, $purchaseReceivedDetail->new_purchased_received_id);
        $this->assertEquals($item_category->id, $purchaseReceivedDetail->item_category_id);
        $this->assertEquals($item->id, $purchaseReceivedDetail->item_id);
        $this->assertEquals($unit_price, $purchaseReceivedDetail->unit_price);
        $this->assertEquals($quantity, $purchaseReceivedDetail->quantity);
        $this->assertEquals($unit->id, $purchaseReceivedDetail->unit_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $purchaseReceivedDetail = PurchaseReceivedDetail::factory()->create();

        $response = $this->delete(route('purchase-received-details.destroy', $purchaseReceivedDetail));

        $response->assertNoContent();

        $this->assertModelMissing($purchaseReceivedDetail);
    }
}
