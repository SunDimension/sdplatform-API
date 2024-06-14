<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\CreditLimit;
use App\Models\CreditSale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditSaleController
 */
final class CreditSaleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $creditSales = CreditSale::factory()->count(3)->create();

        $response = $this->get(route('credit-sales.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditSaleController::class,
            'store',
            \App\Http\Requests\CreditSaleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer = Customer::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        $credit_limit = CreditLimit::factory()->create();
        $credit_amount = $this->faker->word();
        $credit_balance = $this->faker->word();

        $response = $this->post(route('credit-sales.store'), [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'credit_limit' => $credit_limit->id,
            'credit_amount' => $credit_amount,
            'credit_balance' => $credit_balance,
        ]);

        $creditSales = CreditSale::query()
            ->where('customer_id', $customer->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->where('credit_limit', $credit_limit->id)
            ->where('credit_amount', $credit_amount)
            ->where('credit_balance', $credit_balance)
            ->get();
        $this->assertCount(1, $creditSales);
        $creditSale = $creditSales->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $creditSale = CreditSale::factory()->create();

        $response = $this->get(route('credit-sales.show', $creditSale));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditSaleController::class,
            'update',
            \App\Http\Requests\CreditSaleUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $creditSale = CreditSale::factory()->create();
        $customer = Customer::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        $credit_limit = CreditLimit::factory()->create();
        $credit_amount = $this->faker->word();
        $credit_balance = $this->faker->word();

        $response = $this->put(route('credit-sales.update', $creditSale), [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'credit_limit' => $credit_limit->id,
            'credit_amount' => $credit_amount,
            'credit_balance' => $credit_balance,
        ]);

        $creditSale->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer->id, $creditSale->customer_id);
        $this->assertEquals($branch->id, $creditSale->branch_id);
        $this->assertEquals($warehouse->id, $creditSale->warehouse_id);
        $this->assertEquals($product->id, $creditSale->product_id);
        $this->assertEquals($credit_limit->id, $creditSale->credit_limit);
        $this->assertEquals($credit_amount, $creditSale->credit_amount);
        $this->assertEquals($credit_balance, $creditSale->credit_balance);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $creditSale = CreditSale::factory()->create();

        $response = $this->delete(route('credit-sales.destroy', $creditSale));

        $response->assertNoContent();

        $this->assertModelMissing($creditSale);
    }
}
