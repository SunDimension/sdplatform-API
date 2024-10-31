<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ReceiveOrder;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReceiveOrderController
 */
final class ReceiveOrderControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $receiveOrders = ReceiveOrder::factory()->count(3)->create();

        $response = $this->get(route('receive-orders.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReceiveOrderController::class,
            'store',
            \App\Http\Requests\ReceiveOrderStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $purchase_order_number = $this->faker->word();
        $receive_date = Carbon::parse($this->faker->dateTime());
        $store = Store::factory()->create();
        $vendor_id = $this->faker->word();
        $status = $this->faker->word();

        $response = $this->post(route('receive-orders.store'), [
            'purchase_order_number' => $purchase_order_number,
            'receive_date' => $receive_date->toDateTimeString(),
            'store_id' => $store->id,
            'vendor_id' => $vendor_id,
            'status' => $status,
        ]);

        $receiveOrders = ReceiveOrder::query()
            ->where('purchase_order_number', $purchase_order_number)
            ->where('receive_date', $receive_date)
            ->where('store_id', $store->id)
            ->where('vendor_id', $vendor_id)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $receiveOrders);
        $receiveOrder = $receiveOrders->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $receiveOrder = ReceiveOrder::factory()->create();

        $response = $this->get(route('receive-orders.show', $receiveOrder));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReceiveOrderController::class,
            'update',
            \App\Http\Requests\ReceiveOrderUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $receiveOrder = ReceiveOrder::factory()->create();
        $purchase_order_number = $this->faker->word();
        $receive_date = Carbon::parse($this->faker->dateTime());
        $store = Store::factory()->create();
        $vendor_id = $this->faker->word();
        $status = $this->faker->word();

        $response = $this->put(route('receive-orders.update', $receiveOrder), [
            'purchase_order_number' => $purchase_order_number,
            'receive_date' => $receive_date->toDateTimeString(),
            'store_id' => $store->id,
            'vendor_id' => $vendor_id,
            'status' => $status,
        ]);

        $receiveOrder->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($purchase_order_number, $receiveOrder->purchase_order_number);
        $this->assertEquals($receive_date->timestamp, $receiveOrder->receive_date);
        $this->assertEquals($store->id, $receiveOrder->store_id);
        $this->assertEquals($vendor_id, $receiveOrder->vendor_id);
        $this->assertEquals($status, $receiveOrder->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $receiveOrder = ReceiveOrder::factory()->create();

        $response = $this->delete(route('receive-orders.destroy', $receiveOrder));

        $response->assertNoContent();

        $this->assertSoftDeleted($receiveOrder);
    }
}
