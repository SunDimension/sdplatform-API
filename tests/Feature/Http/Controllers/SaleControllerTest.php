<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SaleController
 */
final class SaleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $sales = Sale::factory()->count(3)->create();

        $response = $this->get(route('sales.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SaleController::class,
            'store',
            \App\Http\Requests\SaleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $quantity = $this->faker->randomFloat(/** float_attributes **/);
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $discount = Discount::factory()->create();
        $discount = $this->faker->randomFloat(/** float_attributes **/);
        $sales_order_number = $this->faker->word();
        $total_amount = $this->faker->word();
        $amount_paid = $this->faker->randomFloat(/** float_attributes **/);
        $balance_amount = $this->faker->randomFloat(/** float_attributes **/);
        $payment_mode = PaymentMode::factory()->create();

        $response = $this->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => $quantity,
            'price' => $price,
            'discount_id' => $discount->id,
            'discount' => $discount,
            'sales_order_number' => $sales_order_number,
            'total_amount' => $total_amount,
            'amount_paid' => $amount_paid,
            'balance_amount' => $balance_amount,
            'payment_mode' => $payment_mode->id,
        ]);

        $sales = Sale::query()
            ->where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', $quantity)
            ->where('price', $price)
            ->where('discount_id', $discount->id)
            ->where('discount', $discount)
            ->where('sales_order_number', $sales_order_number)
            ->where('total_amount', $total_amount)
            ->where('amount_paid', $amount_paid)
            ->where('balance_amount', $balance_amount)
            ->where('payment_mode', $payment_mode->id)
            ->get();
        $this->assertCount(1, $sales);
        $sale = $sales->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->get(route('sales.show', $sale));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SaleController::class,
            'update',
            \App\Http\Requests\SaleUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $sale = Sale::factory()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $quantity = $this->faker->randomFloat(/** float_attributes **/);
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $discount = Discount::factory()->create();
        $discount = $this->faker->randomFloat(/** float_attributes **/);
        $sales_order_number = $this->faker->word();
        $total_amount = $this->faker->word();
        $amount_paid = $this->faker->randomFloat(/** float_attributes **/);
        $balance_amount = $this->faker->randomFloat(/** float_attributes **/);
        $payment_mode = PaymentMode::factory()->create();

        $response = $this->put(route('sales.update', $sale), [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => $quantity,
            'price' => $price,
            'discount_id' => $discount->id,
            'discount' => $discount,
            'sales_order_number' => $sales_order_number,
            'total_amount' => $total_amount,
            'amount_paid' => $amount_paid,
            'balance_amount' => $balance_amount,
            'payment_mode' => $payment_mode->id,
        ]);

        $sale->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer->id, $sale->customer_id);
        $this->assertEquals($product->id, $sale->product_id);
        $this->assertEquals($branch->id, $sale->branch_id);
        $this->assertEquals($warehouse->id, $sale->warehouse_id);
        $this->assertEquals($quantity, $sale->quantity);
        $this->assertEquals($price, $sale->price);
        $this->assertEquals($discount->id, $sale->discount_id);
        $this->assertEquals($discount, $sale->discount);
        $this->assertEquals($sales_order_number, $sale->sales_order_number);
        $this->assertEquals($total_amount, $sale->total_amount);
        $this->assertEquals($amount_paid, $sale->amount_paid);
        $this->assertEquals($balance_amount, $sale->balance_amount);
        $this->assertEquals($payment_mode->id, $sale->payment_mode);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->delete(route('sales.destroy', $sale));

        $response->assertNoContent();

        $this->assertModelMissing($sale);
    }
}
