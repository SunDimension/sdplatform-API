<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PaymentMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentModeController
 */
final class PaymentModeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentModes = PaymentMode::factory()->count(3)->create();

        $response = $this->get(route('payment-modes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentModeController::class,
            'store',
            \App\Http\Requests\PaymentModeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('payment-modes.store'), [
            'name' => $name,
        ]);

        $paymentModes = PaymentMode::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $paymentModes);
        $paymentMode = $paymentModes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentMode = PaymentMode::factory()->create();

        $response = $this->get(route('payment-modes.show', $paymentMode));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentModeController::class,
            'update',
            \App\Http\Requests\PaymentModeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentMode = PaymentMode::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('payment-modes.update', $paymentMode), [
            'name' => $name,
        ]);

        $paymentMode->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $paymentMode->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentMode = PaymentMode::factory()->create();

        $response = $this->delete(route('payment-modes.destroy', $paymentMode));

        $response->assertNoContent();

        $this->assertModelMissing($paymentMode);
    }
}
