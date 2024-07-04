<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\FinancialPeriod;
use App\Models\PeriodAccount;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PeriodAccountController
 */
final class PeriodAccountControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $periodAccounts = PeriodAccount::factory()->count(3)->create();

        $response = $this->get(route('period-accounts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountController::class,
            'store',
            \App\Http\Requests\PeriodAccountStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $financial_period = FinancialPeriod::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account = Account::factory()->create();

        $response = $this->post(route('period-accounts.store'), [
            'financial_period_id' => $financial_period->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_id' => $account->id,
        ]);

        $periodAccounts = PeriodAccount::query()
            ->where('financial_period_id', $financial_period->id)
            ->where('debit', $debit)
            ->where('credit', $credit)
            ->where('amount', $amount)
            ->where('warehouse_id', $warehouse->id)
            ->where('account_id', $account->id)
            ->get();
        $this->assertCount(1, $periodAccounts);
        $periodAccount = $periodAccounts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $periodAccount = PeriodAccount::factory()->create();

        $response = $this->get(route('period-accounts.show', $periodAccount));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountController::class,
            'update',
            \App\Http\Requests\PeriodAccountUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $periodAccount = PeriodAccount::factory()->create();
        $financial_period = FinancialPeriod::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account = Account::factory()->create();

        $response = $this->put(route('period-accounts.update', $periodAccount), [
            'financial_period_id' => $financial_period->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_id' => $account->id,
        ]);

        $periodAccount->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($financial_period->id, $periodAccount->financial_period_id);
        $this->assertEquals($debit, $periodAccount->debit);
        $this->assertEquals($credit, $periodAccount->credit);
        $this->assertEquals($amount, $periodAccount->amount);
        $this->assertEquals($warehouse->id, $periodAccount->warehouse_id);
        $this->assertEquals($account->id, $periodAccount->account_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $periodAccount = PeriodAccount::factory()->create();

        $response = $this->delete(route('period-accounts.destroy', $periodAccount));

        $response->assertNoContent();

        $this->assertSoftDeleted($periodAccount);
    }
}
