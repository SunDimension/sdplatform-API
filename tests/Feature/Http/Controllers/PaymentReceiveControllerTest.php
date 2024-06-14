<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PaymentReceive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentReceiveController
 */
final class PaymentReceiveControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentReceives = PaymentReceive::factory()->count(3)->create();

        $response = $this->get(route('payment-receives.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentReceiveController::class,
            'store',
            \App\Http\Requests\PaymentReceiveStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('payment-receives.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(paymentReceives, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentReceive = PaymentReceive::factory()->create();

        $response = $this->get(route('payment-receives.show', $paymentReceive));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentReceiveController::class,
            'update',
            \App\Http\Requests\PaymentReceiveUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentReceive = PaymentReceive::factory()->create();

        $response = $this->put(route('payment-receives.update', $paymentReceive));

        $paymentReceive->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentReceive = PaymentReceive::factory()->create();

        $response = $this->delete(route('payment-receives.destroy', $paymentReceive));

        $response->assertNoContent();

        $this->assertModelMissing($paymentReceive);
    }
}
