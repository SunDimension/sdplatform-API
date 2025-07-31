<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\SalesReceipt;
use App\Models\Tax;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SalesReceiptController
 */
final class SalesReceiptControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $salesReceipts = SalesReceipt::factory()->count(3)->create();

        $response = $this->get(route('sales-receipts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SalesReceiptController::class,
            'store',
            \App\Http\Requests\SalesReceiptStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer = Customer::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $tax = Tax::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $discount = Discount::factory()->create();
        $quantity = $this->faker->word();
        $rate = $this->faker->word();
        $amount = $this->faker->word();
        $receipt_date = Carbon::parse($this->faker->dateTime());
        $customer_note = $this->faker->word();

        $response = $this->post(route('sales-receipts.store'), [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'tax_id' => $tax->id,
            'payment_mode_id' => $payment_mode->id,
            'discount_id' => $discount->id,
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $amount,
            'receipt_date' => $receipt_date->toDateTimeString(),
            'customer_note' => $customer_note,
        ]);

        $salesReceipts = SalesReceipt::query()
            ->where('customer_id', $customer->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('tax_id', $tax->id)
            ->where('payment_mode_id', $payment_mode->id)
            ->where('discount_id', $discount->id)
            ->where('quantity', $quantity)
            ->where('rate', $rate)
            ->where('amount', $amount)
            ->where('receipt_date', $receipt_date)
            ->where('customer_note', $customer_note)
            ->get();
        $this->assertCount(1, $salesReceipts);
        $salesReceipt = $salesReceipts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $salesReceipt = SalesReceipt::factory()->create();

        $response = $this->get(route('sales-receipts.show', $salesReceipt));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SalesReceiptController::class,
            'update',
            \App\Http\Requests\SalesReceiptUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $salesReceipt = SalesReceipt::factory()->create();
        $customer = Customer::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        $tax = Tax::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $discount = Discount::factory()->create();
        $quantity = $this->faker->word();
        $rate = $this->faker->word();
        $amount = $this->faker->word();
        $receipt_date = Carbon::parse($this->faker->dateTime());
        $customer_note = $this->faker->word();

        $response = $this->put(route('sales-receipts.update', $salesReceipt), [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'tax_id' => $tax->id,
            'payment_mode_id' => $payment_mode->id,
            'discount_id' => $discount->id,
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $amount,
            'receipt_date' => $receipt_date->toDateTimeString(),
            'customer_note' => $customer_note,
        ]);

        $salesReceipt->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer->id, $salesReceipt->customer_id);
        $this->assertEquals($branch->id, $salesReceipt->branch_id);
        $this->assertEquals($warehouse->id, $salesReceipt->warehouse_id);
        $this->assertEquals($product->id, $salesReceipt->product_id);
        $this->assertEquals($tax->id, $salesReceipt->tax_id);
        $this->assertEquals($payment_mode->id, $salesReceipt->payment_mode_id);
        $this->assertEquals($discount->id, $salesReceipt->discount_id);
        $this->assertEquals($quantity, $salesReceipt->quantity);
        $this->assertEquals($rate, $salesReceipt->rate);
        $this->assertEquals($amount, $salesReceipt->amount);
        $this->assertEquals($receipt_date->timestamp, $salesReceipt->receipt_date);
        $this->assertEquals($customer_note, $salesReceipt->customer_note);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $salesReceipt = SalesReceipt::factory()->create();

        $response = $this->delete(route('sales-receipts.destroy', $salesReceipt));

        $response->assertNoContent();

        $this->assertModelMissing($salesReceipt);
    }

    #[Test]
    public function generate_accounting_entries_creates_journal_entries(): void
    {
        $salesReceipt = SalesReceipt::factory()->create([
            'sales_receipt_number' => 'HGV-SR-1234567',
            'amount_paid' => 1000.00,
            'payment_type' => 'Cash'
        ]);

        $response = $this->post("/api/sales-receipts/{$salesReceipt->id}/generate-accounting-entries");

        $response->assertOk();
        $response->assertJson([
            'message' => 'Accounting entries generated successfully'
        ]);

        // Verify that journal entries were created
        $this->assertDatabaseHas('transaction_journal_entries', [
            'description' => "Sales Receipt #{$salesReceipt->sales_receipt_number}"
        ]);
    }

    #[Test]
    public function get_accounting_entries_returns_journal_entries(): void
    {
        $salesReceipt = SalesReceipt::factory()->create([
            'sales_receipt_number' => 'HGV-SR-1234567'
        ]);

        $response = $this->get("/api/sales-receipts/{$salesReceipt->id}/accounting-entries");

        $response->assertOk();
        $response->assertJson([
            'message' => 'Accounting entries retrieved successfully'
        ]);
    }

    #[Test]
    public function generate_bulk_accounting_entries_processes_multiple_receipts(): void
    {
        $salesReceipt1 = SalesReceipt::factory()->create([
            'sales_receipt_number' => 'HGV-SR-1111111',
            'amount_paid' => 500.00
        ]);
        
        $salesReceipt2 = SalesReceipt::factory()->create([
            'sales_receipt_number' => 'HGV-SR-2222222',
            'amount_paid' => 750.00
        ]);

        $response = $this->post('/api/sales-receipts/generate-bulk-accounting-entries', [
            'sales_receipt_ids' => [$salesReceipt1->id, $salesReceipt2->id]
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Bulk accounting entries generation completed'
        ]);
    }
}
