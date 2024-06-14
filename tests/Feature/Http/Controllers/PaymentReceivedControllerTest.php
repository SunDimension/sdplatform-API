<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\DepositBank;
use App\Models\PaymentMode;
use App\Models\PaymentReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentReceivedController
 */
final class PaymentReceivedControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentReceiveds = PaymentReceived::factory()->count(3)->create();

        $response = $this->get(route('payment-receiveds.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentReceivedController::class,
            'store',
            \App\Http\Requests\PaymentReceivedStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer = Customer::factory()->create();
        $amount_received = $this->faker->word();
        $bank_charges = $this->faker->randomFloat(/** float_attributes **/);
        $payment_number = $this->faker->word();
        $deposit_bank = DepositBank::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $invoice_number = $this->faker->word();

        $response = $this->post(route('payment-receiveds.store'), [
            'customer_id' => $customer->id,
            'amount_received' => $amount_received,
            'bank_charges' => $bank_charges,
            'payment_number' => $payment_number,
            'deposit_bank_id' => $deposit_bank->id,
            'payment_mode_id' => $payment_mode->id,
            'invoice_number' => $invoice_number,
        ]);

        $paymentReceiveds = PaymentReceived::query()
            ->where('customer_id', $customer->id)
            ->where('amount_received', $amount_received)
            ->where('bank_charges', $bank_charges)
            ->where('payment_number', $payment_number)
            ->where('deposit_bank_id', $deposit_bank->id)
            ->where('payment_mode_id', $payment_mode->id)
            ->where('invoice_number', $invoice_number)
            ->get();
        $this->assertCount(1, $paymentReceiveds);
        $paymentReceived = $paymentReceiveds->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentReceived = PaymentReceived::factory()->create();

        $response = $this->get(route('payment-receiveds.show', $paymentReceived));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentReceivedController::class,
            'update',
            \App\Http\Requests\PaymentReceivedUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentReceived = PaymentReceived::factory()->create();
        $customer = Customer::factory()->create();
        $amount_received = $this->faker->word();
        $bank_charges = $this->faker->randomFloat(/** float_attributes **/);
        $payment_number = $this->faker->word();
        $deposit_bank = DepositBank::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $invoice_number = $this->faker->word();

        $response = $this->put(route('payment-receiveds.update', $paymentReceived), [
            'customer_id' => $customer->id,
            'amount_received' => $amount_received,
            'bank_charges' => $bank_charges,
            'payment_number' => $payment_number,
            'deposit_bank_id' => $deposit_bank->id,
            'payment_mode_id' => $payment_mode->id,
            'invoice_number' => $invoice_number,
        ]);

        $paymentReceived->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer->id, $paymentReceived->customer_id);
        $this->assertEquals($amount_received, $paymentReceived->amount_received);
        $this->assertEquals($bank_charges, $paymentReceived->bank_charges);
        $this->assertEquals($payment_number, $paymentReceived->payment_number);
        $this->assertEquals($deposit_bank->id, $paymentReceived->deposit_bank_id);
        $this->assertEquals($payment_mode->id, $paymentReceived->payment_mode_id);
        $this->assertEquals($invoice_number, $paymentReceived->invoice_number);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentReceived = PaymentReceived::factory()->create();

        $response = $this->delete(route('payment-receiveds.destroy', $paymentReceived));

        $response->assertNoContent();

        $this->assertModelMissing($paymentReceived);
    }
}
