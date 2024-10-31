<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Models\StoreTransferItem;
use App\Models\TransferOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StoreTransferItemController
 */
final class StoreTransferItemControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $storeTransferItems = StoreTransferItem::factory()->count(3)->create();

        $response = $this->get(route('store-transfer-items.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StoreTransferItemController::class,
            'store',
            \App\Http\Requests\StoreTransferItemStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $transfer_order = TransferOrder::factory()->create();
        $quantity = $this->faker->word();
        $unit_price = $this->faker->randomFloat(/** double_attributes **/);
        $product = Product::factory()->create();

        $response = $this->post(route('store-transfer-items.store'), [
            'transfer_order_id' => $transfer_order->id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'product_id' => $product->id,
        ]);

        $storeTransferItems = StoreTransferItem::query()
            ->where('transfer_order_id', $transfer_order->id)
            ->where('quantity', $quantity)
            ->where('unit_price', $unit_price)
            ->where('product_id', $product->id)
            ->get();
        $this->assertCount(1, $storeTransferItems);
        $storeTransferItem = $storeTransferItems->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $storeTransferItem = StoreTransferItem::factory()->create();

        $response = $this->get(route('store-transfer-items.show', $storeTransferItem));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StoreTransferItemController::class,
            'update',
            \App\Http\Requests\StoreTransferItemUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $storeTransferItem = StoreTransferItem::factory()->create();
        $transfer_order = TransferOrder::factory()->create();
        $quantity = $this->faker->word();
        $unit_price = $this->faker->randomFloat(/** double_attributes **/);
        $product = Product::factory()->create();

        $response = $this->put(route('store-transfer-items.update', $storeTransferItem), [
            'transfer_order_id' => $transfer_order->id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'product_id' => $product->id,
        ]);

        $storeTransferItem->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($transfer_order->id, $storeTransferItem->transfer_order_id);
        $this->assertEquals($quantity, $storeTransferItem->quantity);
        $this->assertEquals($unit_price, $storeTransferItem->unit_price);
        $this->assertEquals($product->id, $storeTransferItem->product_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $storeTransferItem = StoreTransferItem::factory()->create();

        $response = $this->delete(route('store-transfer-items.destroy', $storeTransferItem));

        $response->assertNoContent();

        $this->assertSoftDeleted($storeTransferItem);
    }
}
