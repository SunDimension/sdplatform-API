<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DestinationWarehouse;
use App\Models\Item;
use App\Models\SourceWarehouse;
use App\Models\TransferOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TransferOrderController
 */
final class TransferOrderControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $transferOrders = TransferOrder::factory()->count(3)->create();

        $response = $this->get(route('transfer-orders.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransferOrderController::class,
            'store',
            \App\Http\Requests\TransferOrderStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $transfer_order_number = $this->faker->word();
        $transfer_date = Carbon::parse($this->faker->dateTime());
        $transfer_reason = $this->faker->word();
        $source_warehouse = SourceWarehouse::factory()->create();
        $destination_warehouse = DestinationWarehouse::factory()->create();
        $image_url = $this->faker->word();
        $transfer_quantity = $this->faker->word();
        $item = Item::factory()->create();

        $response = $this->post(route('transfer-orders.store'), [
            'transfer_order_number' => $transfer_order_number,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'transfer_reason' => $transfer_reason,
            'source_warehouse_id' => $source_warehouse->id,
            'destination_warehouse_id' => $destination_warehouse->id,
            'image_url' => $image_url,
            'transfer_quantity' => $transfer_quantity,
            'item_id' => $item->id,
        ]);

        $transferOrders = TransferOrder::query()
            ->where('transfer_order_number', $transfer_order_number)
            ->where('transfer_date', $transfer_date)
            ->where('transfer_reason', $transfer_reason)
            ->where('source_warehouse_id', $source_warehouse->id)
            ->where('destination_warehouse_id', $destination_warehouse->id)
            ->where('image_url', $image_url)
            ->where('transfer_quantity', $transfer_quantity)
            ->where('item_id', $item->id)
            ->get();
        $this->assertCount(1, $transferOrders);
        $transferOrder = $transferOrders->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $transferOrder = TransferOrder::factory()->create();

        $response = $this->get(route('transfer-orders.show', $transferOrder));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransferOrderController::class,
            'update',
            \App\Http\Requests\TransferOrderUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $transferOrder = TransferOrder::factory()->create();
        $transfer_order_number = $this->faker->word();
        $transfer_date = Carbon::parse($this->faker->dateTime());
        $transfer_reason = $this->faker->word();
        $source_warehouse = SourceWarehouse::factory()->create();
        $destination_warehouse = DestinationWarehouse::factory()->create();
        $image_url = $this->faker->word();
        $transfer_quantity = $this->faker->word();
        $item = Item::factory()->create();

        $response = $this->put(route('transfer-orders.update', $transferOrder), [
            'transfer_order_number' => $transfer_order_number,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'transfer_reason' => $transfer_reason,
            'source_warehouse_id' => $source_warehouse->id,
            'destination_warehouse_id' => $destination_warehouse->id,
            'image_url' => $image_url,
            'transfer_quantity' => $transfer_quantity,
            'item_id' => $item->id,
        ]);

        $transferOrder->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($transfer_order_number, $transferOrder->transfer_order_number);
        $this->assertEquals($transfer_date->timestamp, $transferOrder->transfer_date);
        $this->assertEquals($transfer_reason, $transferOrder->transfer_reason);
        $this->assertEquals($source_warehouse->id, $transferOrder->source_warehouse_id);
        $this->assertEquals($destination_warehouse->id, $transferOrder->destination_warehouse_id);
        $this->assertEquals($image_url, $transferOrder->image_url);
        $this->assertEquals($transfer_quantity, $transferOrder->transfer_quantity);
        $this->assertEquals($item->id, $transferOrder->item_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $transferOrder = TransferOrder::factory()->create();

        $response = $this->delete(route('transfer-orders.destroy', $transferOrder));

        $response->assertNoContent();

        $this->assertModelMissing($transferOrder);
    }
}
