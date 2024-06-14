<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Item;
use App\Models\PaymentVoucherDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentVoucherDetailController
 */
final class PaymentVoucherDetailControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentVoucherDetails = PaymentVoucherDetail::factory()->count(3)->create();

        $response = $this->get(route('payment-voucher-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentVoucherDetailController::class,
            'store',
            \App\Http\Requests\PaymentVoucherDetailStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $Expense_account_id = $this->faker->word();
        $amount = $this->faker->word();
        $quantity = $this->faker->word();
        $item = Item::factory()->create();

        $response = $this->post(route('payment-voucher-details.store'), [
            'Expense_account_id' => $Expense_account_id,
            'amount' => $amount,
            'quantity' => $quantity,
            'item_id' => $item->id,
        ]);

        $paymentVoucherDetails = PaymentVoucherDetail::query()
            ->where('Expense_account_id', $Expense_account_id)
            ->where('amount', $amount)
            ->where('quantity', $quantity)
            ->where('item_id', $item->id)
            ->get();
        $this->assertCount(1, $paymentVoucherDetails);
        $paymentVoucherDetail = $paymentVoucherDetails->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentVoucherDetail = PaymentVoucherDetail::factory()->create();

        $response = $this->get(route('payment-voucher-details.show', $paymentVoucherDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentVoucherDetailController::class,
            'update',
            \App\Http\Requests\PaymentVoucherDetailUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentVoucherDetail = PaymentVoucherDetail::factory()->create();
        $Expense_account_id = $this->faker->word();
        $amount = $this->faker->word();
        $quantity = $this->faker->word();
        $item = Item::factory()->create();

        $response = $this->put(route('payment-voucher-details.update', $paymentVoucherDetail), [
            'Expense_account_id' => $Expense_account_id,
            'amount' => $amount,
            'quantity' => $quantity,
            'item_id' => $item->id,
        ]);

        $paymentVoucherDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($Expense_account_id, $paymentVoucherDetail->Expense_account_id);
        $this->assertEquals($amount, $paymentVoucherDetail->amount);
        $this->assertEquals($quantity, $paymentVoucherDetail->quantity);
        $this->assertEquals($item->id, $paymentVoucherDetail->item_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentVoucherDetail = PaymentVoucherDetail::factory()->create();

        $response = $this->delete(route('payment-voucher-details.destroy', $paymentVoucherDetail));

        $response->assertNoContent();

        $this->assertModelMissing($paymentVoucherDetail);
    }
}
