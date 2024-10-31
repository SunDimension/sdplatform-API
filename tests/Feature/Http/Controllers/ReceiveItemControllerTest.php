<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Product;
use App\Models\ReceiveItem;
use App\Models\ReceiveOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReceiveItemController
 */
final class ReceiveItemControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $receiveItems = ReceiveItem::factory()->count(3)->create();

        $response = $this->get(route('receive-items.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReceiveItemController::class,
            'store',
            \App\Http\Requests\ReceiveItemStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $receive_order = ReceiveOrder::factory()->create();
        $quantity = $this->faker->word();
        $unit_price = $this->faker->randomFloat(/** double_attributes **/);
        $product = Product::factory()->create();

        $response = $this->post(route('receive-items.store'), [
            'receive_order_id' => $receive_order->id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'product_id' => $product->id,
        ]);

        $receiveItems = ReceiveItem::query()
            ->where('receive_order_id', $receive_order->id)
            ->where('quantity', $quantity)
            ->where('unit_price', $unit_price)
            ->where('product_id', $product->id)
            ->get();
        $this->assertCount(1, $receiveItems);
        $receiveItem = $receiveItems->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $receiveItem = ReceiveItem::factory()->create();

        $response = $this->get(route('receive-items.show', $receiveItem));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReceiveItemController::class,
            'update',
            \App\Http\Requests\ReceiveItemUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $receiveItem = ReceiveItem::factory()->create();
        $receive_order = ReceiveOrder::factory()->create();
        $quantity = $this->faker->word();
        $unit_price = $this->faker->randomFloat(/** double_attributes **/);
        $product = Product::factory()->create();

        $response = $this->put(route('receive-items.update', $receiveItem), [
            'receive_order_id' => $receive_order->id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'product_id' => $product->id,
        ]);

        $receiveItem->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($receive_order->id, $receiveItem->receive_order_id);
        $this->assertEquals($quantity, $receiveItem->quantity);
        $this->assertEquals($unit_price, $receiveItem->unit_price);
        $this->assertEquals($product->id, $receiveItem->product_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $receiveItem = ReceiveItem::factory()->create();

        $response = $this->delete(route('receive-items.destroy', $receiveItem));

        $response->assertNoContent();

        $this->assertSoftDeleted($receiveItem);
    }
}
