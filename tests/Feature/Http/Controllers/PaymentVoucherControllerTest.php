<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\PaymentMode;
use App\Models\PaymentVoucher;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PaymentVoucherController
 */
final class PaymentVoucherControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $paymentVouchers = PaymentVoucher::factory()->count(3)->create();

        $response = $this->get(route('payment-vouchers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentVoucherController::class,
            'store',
            \App\Http\Requests\PaymentVoucherStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $product = Product::factory()->create();
        $expense_date = Carbon::parse($this->faker->dateTime());
        $amount = $this->faker->word();
        $description = $this->faker->text();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $tax = Tax::factory()->create();
        $vendor = Vendor::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $expense_account_id = $this->faker->word();

        $response = $this->post(route('payment-vouchers.store'), [
            'product_id' => $product->id,
            'expense_date' => $expense_date->toDateTimeString(),
            'amount' => $amount,
            'description' => $description,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'tax_id' => $tax->id,
            'vendor_id' => $vendor->id,
            'payment_mode_id' => $payment_mode->id,
            'expense_account_id' => $expense_account_id,
        ]);

        $paymentVouchers = PaymentVoucher::query()
            ->where('product_id', $product->id)
            ->where('expense_date', $expense_date)
            ->where('amount', $amount)
            ->where('description', $description)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('tax_id', $tax->id)
            ->where('vendor_id', $vendor->id)
            ->where('payment_mode_id', $payment_mode->id)
            ->where('expense_account_id', $expense_account_id)
            ->get();
        $this->assertCount(1, $paymentVouchers);
        $paymentVoucher = $paymentVouchers->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $paymentVoucher = PaymentVoucher::factory()->create();

        $response = $this->get(route('payment-vouchers.show', $paymentVoucher));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PaymentVoucherController::class,
            'update',
            \App\Http\Requests\PaymentVoucherUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $paymentVoucher = PaymentVoucher::factory()->create();
        $product = Product::factory()->create();
        $expense_date = Carbon::parse($this->faker->dateTime());
        $amount = $this->faker->word();
        $description = $this->faker->text();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $tax = Tax::factory()->create();
        $vendor = Vendor::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $expense_account_id = $this->faker->word();

        $response = $this->put(route('payment-vouchers.update', $paymentVoucher), [
            'product_id' => $product->id,
            'expense_date' => $expense_date->toDateTimeString(),
            'amount' => $amount,
            'description' => $description,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'tax_id' => $tax->id,
            'vendor_id' => $vendor->id,
            'payment_mode_id' => $payment_mode->id,
            'expense_account_id' => $expense_account_id,
        ]);

        $paymentVoucher->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($product->id, $paymentVoucher->product_id);
        $this->assertEquals($expense_date->timestamp, $paymentVoucher->expense_date);
        $this->assertEquals($amount, $paymentVoucher->amount);
        $this->assertEquals($description, $paymentVoucher->description);
        $this->assertEquals($branch->id, $paymentVoucher->branch_id);
        $this->assertEquals($warehouse->id, $paymentVoucher->warehouse_id);
        $this->assertEquals($tax->id, $paymentVoucher->tax_id);
        $this->assertEquals($vendor->id, $paymentVoucher->vendor_id);
        $this->assertEquals($payment_mode->id, $paymentVoucher->payment_mode_id);
        $this->assertEquals($expense_account_id, $paymentVoucher->expense_account_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $paymentVoucher = PaymentVoucher::factory()->create();

        $response = $this->delete(route('payment-vouchers.destroy', $paymentVoucher));

        $response->assertNoContent();

        $this->assertModelMissing($paymentVoucher);
    }
}
