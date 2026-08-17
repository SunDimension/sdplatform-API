<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Payment;
use App\Models\Payments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentsController
 */
final class PaymentsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $payments = Payments::factory()->count(3)->create();

        $response = $this->get(route('payments.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentsController::class,
            'store',
            \App\Http\Requests\PaymentsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $user = User::factory()->create();
        $currency = $this->faker->word();
        $gateway = $this->faker->word();
        $transaction_reference = $this->faker->word();
        $amount = $this->faker->randomFloat(/** float_attributes **/);
        $payment_status = $this->faker->word();

        $response = $this->post(route('payments.store'), [
            'user_id' => $user->id,
            'currency' => $currency,
            'gateway' => $gateway,
            'transaction_reference' => $transaction_reference,
            'amount' => $amount,
            'payment_status' => $payment_status,
        ]);

        $payments = Payment::query()
            ->where('user_id', $user->id)
            ->where('currency', $currency)
            ->where('gateway', $gateway)
            ->where('transaction_reference', $transaction_reference)
            ->where('amount', $amount)
            ->where('payment_status', $payment_status)
            ->get();
        $this->assertCount(1, $payments);
        $payment = $payments->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $payment = Payments::factory()->create();

        $response = $this->get(route('payments.show', $payment));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentsController::class,
            'update',
            \App\Http\Requests\PaymentsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $payment = Payments::factory()->create();
        $user = User::factory()->create();
        $currency = $this->faker->word();
        $gateway = $this->faker->word();
        $transaction_reference = $this->faker->word();
        $amount = $this->faker->randomFloat(/** float_attributes **/);
        $payment_status = $this->faker->word();

        $response = $this->put(route('payments.update', $payment), [
            'user_id' => $user->id,
            'currency' => $currency,
            'gateway' => $gateway,
            'transaction_reference' => $transaction_reference,
            'amount' => $amount,
            'payment_status' => $payment_status,
        ]);

        $payment->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $payment->user_id);
        $this->assertEquals($currency, $payment->currency);
        $this->assertEquals($gateway, $payment->gateway);
        $this->assertEquals($transaction_reference, $payment->transaction_reference);
        $this->assertEquals($amount, $payment->amount);
        $this->assertEquals($payment_status, $payment->payment_status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $payment = Payments::factory()->create();
        $payment = Payment::factory()->create();

        $response = $this->delete(route('payments.destroy', $payment));

        $response->assertNoContent();

        $this->assertModelMissing($payment);
    }
}
