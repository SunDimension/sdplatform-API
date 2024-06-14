<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Carrier;
use App\Models\Customer;
use App\Models\Delivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DeliveryController
 */
final class DeliveryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $deliveries = Delivery::factory()->count(3)->create();

        $response = $this->get(route('deliveries.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DeliveryController::class,
            'store',
            \App\Http\Requests\DeliveryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer = Customer::factory()->create();
        $sales_order_number = $this->faker->word();
        $delivery_order_number = $this->faker->word();
        $delivery_date = Carbon::parse($this->faker->dateTime());
        $carrier = Carrier::factory()->create();
        $notes = $this->faker->word();

        $response = $this->post(route('deliveries.store'), [
            'customer_id' => $customer->id,
            'sales_order_number' => $sales_order_number,
            'delivery_order_number' => $delivery_order_number,
            'delivery_date' => $delivery_date->toDateTimeString(),
            'carrier_id' => $carrier->id,
            'notes' => $notes,
        ]);

        $deliveries = Delivery::query()
            ->where('customer_id', $customer->id)
            ->where('sales_order_number', $sales_order_number)
            ->where('delivery_order_number', $delivery_order_number)
            ->where('delivery_date', $delivery_date)
            ->where('carrier_id', $carrier->id)
            ->where('notes', $notes)
            ->get();
        $this->assertCount(1, $deliveries);
        $delivery = $deliveries->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $delivery = Delivery::factory()->create();

        $response = $this->get(route('deliveries.show', $delivery));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DeliveryController::class,
            'update',
            \App\Http\Requests\DeliveryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $delivery = Delivery::factory()->create();
        $customer = Customer::factory()->create();
        $sales_order_number = $this->faker->word();
        $delivery_order_number = $this->faker->word();
        $delivery_date = Carbon::parse($this->faker->dateTime());
        $carrier = Carrier::factory()->create();
        $notes = $this->faker->word();

        $response = $this->put(route('deliveries.update', $delivery), [
            'customer_id' => $customer->id,
            'sales_order_number' => $sales_order_number,
            'delivery_order_number' => $delivery_order_number,
            'delivery_date' => $delivery_date->toDateTimeString(),
            'carrier_id' => $carrier->id,
            'notes' => $notes,
        ]);

        $delivery->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer->id, $delivery->customer_id);
        $this->assertEquals($sales_order_number, $delivery->sales_order_number);
        $this->assertEquals($delivery_order_number, $delivery->delivery_order_number);
        $this->assertEquals($delivery_date->timestamp, $delivery->delivery_date);
        $this->assertEquals($carrier->id, $delivery->carrier_id);
        $this->assertEquals($notes, $delivery->notes);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $delivery = Delivery::factory()->create();

        $response = $this->delete(route('deliveries.destroy', $delivery));

        $response->assertNoContent();

        $this->assertModelMissing($delivery);
    }
}
