<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Tax;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InvoiceController
 */
final class InvoiceControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $invoices = Invoice::factory()->count(3)->create();

        $response = $this->get(route('invoices.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InvoiceController::class,
            'store',
            \App\Http\Requests\InvoiceStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $customer = Customer::factory()->create();
        $invoice_number = $this->faker->word();
        $order_number = $this->faker->word();
        $invoice_date = Carbon::parse($this->faker->dateTime());
        $item = Item::factory()->create();
        $rate = $this->faker->word();
        $quantity = $this->faker->word();
        $discount = Discount::factory()->create();
        $tax = Tax::factory()->create();
        $amount = $this->faker->word();

        $response = $this->post(route('invoices.store'), [
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'customer_id' => $customer->id,
            'invoice_number' => $invoice_number,
            'order_number' => $order_number,
            'invoice_date' => $invoice_date->toDateTimeString(),
            'item_id' => $item->id,
            'rate' => $rate,
            'quantity' => $quantity,
            'discount_id' => $discount->id,
            'tax_id' => $tax->id,
            'amount' => $amount,
        ]);

        $invoices = Invoice::query()
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('customer_id', $customer->id)
            ->where('invoice_number', $invoice_number)
            ->where('order_number', $order_number)
            ->where('invoice_date', $invoice_date)
            ->where('item_id', $item->id)
            ->where('rate', $rate)
            ->where('quantity', $quantity)
            ->where('discount_id', $discount->id)
            ->where('tax_id', $tax->id)
            ->where('amount', $amount)
            ->get();
        $this->assertCount(1, $invoices);
        $invoice = $invoices->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('invoices.show', $invoice));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InvoiceController::class,
            'update',
            \App\Http\Requests\InvoiceUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $invoice = Invoice::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $customer = Customer::factory()->create();
        $invoice_number = $this->faker->word();
        $order_number = $this->faker->word();
        $invoice_date = Carbon::parse($this->faker->dateTime());
        $item = Item::factory()->create();
        $rate = $this->faker->word();
        $quantity = $this->faker->word();
        $discount = Discount::factory()->create();
        $tax = Tax::factory()->create();
        $amount = $this->faker->word();

        $response = $this->put(route('invoices.update', $invoice), [
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'customer_id' => $customer->id,
            'invoice_number' => $invoice_number,
            'order_number' => $order_number,
            'invoice_date' => $invoice_date->toDateTimeString(),
            'item_id' => $item->id,
            'rate' => $rate,
            'quantity' => $quantity,
            'discount_id' => $discount->id,
            'tax_id' => $tax->id,
            'amount' => $amount,
        ]);

        $invoice->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($branch->id, $invoice->branch_id);
        $this->assertEquals($warehouse->id, $invoice->warehouse_id);
        $this->assertEquals($customer->id, $invoice->customer_id);
        $this->assertEquals($invoice_number, $invoice->invoice_number);
        $this->assertEquals($order_number, $invoice->order_number);
        $this->assertEquals($invoice_date->timestamp, $invoice->invoice_date);
        $this->assertEquals($item->id, $invoice->item_id);
        $this->assertEquals($rate, $invoice->rate);
        $this->assertEquals($quantity, $invoice->quantity);
        $this->assertEquals($discount->id, $invoice->discount_id);
        $this->assertEquals($tax->id, $invoice->tax_id);
        $this->assertEquals($amount, $invoice->amount);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->delete(route('invoices.destroy', $invoice));

        $response->assertNoContent();

        $this->assertModelMissing($invoice);
    }
}
