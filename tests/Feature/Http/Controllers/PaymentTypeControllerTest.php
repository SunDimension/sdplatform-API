<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentTypeController
 */
final class PaymentTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentTypes = PaymentType::factory()->count(3)->create();

        $response = $this->get(route('payment-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentTypeController::class,
            'store',
            \App\Http\Requests\PaymentTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('payment-types.store'), [
            'name' => $name,
        ]);

        $paymentTypes = PaymentType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $paymentTypes);
        $paymentType = $paymentTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentType = PaymentType::factory()->create();

        $response = $this->get(route('payment-types.show', $paymentType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentTypeController::class,
            'update',
            \App\Http\Requests\PaymentTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentType = PaymentType::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('payment-types.update', $paymentType), [
            'name' => $name,
        ]);

        $paymentType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $paymentType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentType = PaymentType::factory()->create();

        $response = $this->delete(route('payment-types.destroy', $paymentType));

        $response->assertNoContent();

        $this->assertModelMissing($paymentType);
    }
}
