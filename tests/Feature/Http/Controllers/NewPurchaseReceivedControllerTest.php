<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\NewPurchaseReceived;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NewPurchaseReceivedController
 */
final class NewPurchaseReceivedControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $newPurchaseReceiveds = NewPurchaseReceived::factory()->count(3)->create();

        $response = $this->get(route('new-purchase-receiveds.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseReceivedController::class,
            'store',
            \App\Http\Requests\NewPurchaseReceivedStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $vendor = Vendor::factory()->create();
        $purchase_order_number = $this->faker->word();
        $purchase_received_number = $this->faker->word();
        $received_date = Carbon::parse($this->faker->date());

        $response = $this->post(route('new-purchase-receiveds.store'), [
            'vendor_id' => $vendor->id,
            'purchase_order_number' => $purchase_order_number,
            'purchase_received_number' => $purchase_received_number,
            'received_date' => $received_date->toDateString(),
        ]);

        $newPurchaseReceiveds = NewPurchaseReceived::query()
            ->where('vendor_id', $vendor->id)
            ->where('purchase_order_number', $purchase_order_number)
            ->where('purchase_received_number', $purchase_received_number)
            ->where('received_date', $received_date)
            ->get();
        $this->assertCount(1, $newPurchaseReceiveds);
        $newPurchaseReceived = $newPurchaseReceiveds->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $newPurchaseReceived = NewPurchaseReceived::factory()->create();

        $response = $this->get(route('new-purchase-receiveds.show', $newPurchaseReceived));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseReceivedController::class,
            'update',
            \App\Http\Requests\NewPurchaseReceivedUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $newPurchaseReceived = NewPurchaseReceived::factory()->create();
        $vendor = Vendor::factory()->create();
        $purchase_order_number = $this->faker->word();
        $purchase_received_number = $this->faker->word();
        $received_date = Carbon::parse($this->faker->date());

        $response = $this->put(route('new-purchase-receiveds.update', $newPurchaseReceived), [
            'vendor_id' => $vendor->id,
            'purchase_order_number' => $purchase_order_number,
            'purchase_received_number' => $purchase_received_number,
            'received_date' => $received_date->toDateString(),
        ]);

        $newPurchaseReceived->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($vendor->id, $newPurchaseReceived->vendor_id);
        $this->assertEquals($purchase_order_number, $newPurchaseReceived->purchase_order_number);
        $this->assertEquals($purchase_received_number, $newPurchaseReceived->purchase_received_number);
        $this->assertEquals($received_date, $newPurchaseReceived->received_date);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $newPurchaseReceived = NewPurchaseReceived::factory()->create();

        $response = $this->delete(route('new-purchase-receiveds.destroy', $newPurchaseReceived));

        $response->assertNoContent();

        $this->assertModelMissing($newPurchaseReceived);
    }
}
