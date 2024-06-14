<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentTermController
 */
final class PaymentTermControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentTerms = PaymentTerm::factory()->count(3)->create();

        $response = $this->get(route('payment-terms.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentTermController::class,
            'store',
            \App\Http\Requests\PaymentTermStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('payment-terms.store'), [
            'name' => $name,
        ]);

        $paymentTerms = PaymentTerm::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $paymentTerms);
        $paymentTerm = $paymentTerms->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentTerm = PaymentTerm::factory()->create();

        $response = $this->get(route('payment-terms.show', $paymentTerm));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentTermController::class,
            'update',
            \App\Http\Requests\PaymentTermUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentTerm = PaymentTerm::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('payment-terms.update', $paymentTerm), [
            'name' => $name,
        ]);

        $paymentTerm->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $paymentTerm->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentTerm = PaymentTerm::factory()->create();

        $response = $this->delete(route('payment-terms.destroy', $paymentTerm));

        $response->assertNoContent();

        $this->assertModelMissing($paymentTerm);
    }
}
