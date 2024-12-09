<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\CreatedBy;
use App\Models\CreditTransaction;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditTransactionController
 */
final class CreditTransactionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $creditTransactions = CreditTransaction::factory()->count(3)->create();

        $response = $this->get(route('credit-transactions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTransactionController::class,
            'store',
            \App\Http\Requests\CreditTransactionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create();
        $amount = $this->faker->word();
        $type = $this->faker->randomElement(/** enum_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('credit-transactions.store'), [
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'type' => $type,
            'created_by' => $created_by->id,
        ]);

        $creditTransactions = CreditTransaction::query()
            ->where('branch_id', $branch->id)
            ->where('customer_id', $customer->id)
            ->where('amount', $amount)
            ->where('type', $type)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $creditTransactions);
        $creditTransaction = $creditTransactions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $creditTransaction = CreditTransaction::factory()->create();

        $response = $this->get(route('credit-transactions.show', $creditTransaction));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTransactionController::class,
            'update',
            \App\Http\Requests\CreditTransactionUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $creditTransaction = CreditTransaction::factory()->create();
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create();
        $amount = $this->faker->word();
        $type = $this->faker->randomElement(/** enum_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('credit-transactions.update', $creditTransaction), [
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'type' => $type,
            'created_by' => $created_by->id,
        ]);

        $creditTransaction->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($branch->id, $creditTransaction->branch_id);
        $this->assertEquals($customer->id, $creditTransaction->customer_id);
        $this->assertEquals($amount, $creditTransaction->amount);
        $this->assertEquals($type, $creditTransaction->type);
        $this->assertEquals($created_by->id, $creditTransaction->created_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $creditTransaction = CreditTransaction::factory()->create();

        $response = $this->delete(route('credit-transactions.destroy', $creditTransaction));

        $response->assertNoContent();

        $this->assertSoftDeleted($creditTransaction);
    }
}
