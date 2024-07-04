<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use App\Models\ModifiedBy;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AccountOpeningBalanceController
 */
final class AccountOpeningBalanceControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $accountOpeningBalances = AccountOpeningBalance::factory()->count(3)->create();

        $response = $this->get(route('account-opening-balances.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountOpeningBalanceController::class,
            'store',
            \App\Http\Requests\AccountOpeningBalanceStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $financial_year = FinancialYear::factory()->create();
        $financial_period = FinancialPeriod::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('account-opening-balances.store'), [
            'financial_year_id' => $financial_year->id,
            'financial_period_id' => $financial_period->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountOpeningBalances = AccountOpeningBalance::query()
            ->where('financial_year_id', $financial_year->id)
            ->where('financial_period_id', $financial_period->id)
            ->where('debit', $debit)
            ->where('credit', $credit)
            ->where('amount', $amount)
            ->where('warehouse_id', $warehouse->id)
            ->where('account_no', $account_no)
            ->where('account_id', $account->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $accountOpeningBalances);
        $accountOpeningBalance = $accountOpeningBalances->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $accountOpeningBalance = AccountOpeningBalance::factory()->create();

        $response = $this->get(route('account-opening-balances.show', $accountOpeningBalance));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountOpeningBalanceController::class,
            'update',
            \App\Http\Requests\AccountOpeningBalanceUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $accountOpeningBalance = AccountOpeningBalance::factory()->create();
        $financial_year = FinancialYear::factory()->create();
        $financial_period = FinancialPeriod::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('account-opening-balances.update', $accountOpeningBalance), [
            'financial_year_id' => $financial_year->id,
            'financial_period_id' => $financial_period->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountOpeningBalance->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($financial_year->id, $accountOpeningBalance->financial_year_id);
        $this->assertEquals($financial_period->id, $accountOpeningBalance->financial_period_id);
        $this->assertEquals($debit, $accountOpeningBalance->debit);
        $this->assertEquals($credit, $accountOpeningBalance->credit);
        $this->assertEquals($amount, $accountOpeningBalance->amount);
        $this->assertEquals($warehouse->id, $accountOpeningBalance->warehouse_id);
        $this->assertEquals($account_no, $accountOpeningBalance->account_no);
        $this->assertEquals($account->id, $accountOpeningBalance->account_id);
        $this->assertEquals($created_by->id, $accountOpeningBalance->created_by);
        $this->assertEquals($modified_by->id, $accountOpeningBalance->modified_by);
        $this->assertEquals($deleted_by->id, $accountOpeningBalance->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $accountOpeningBalance = AccountOpeningBalance::factory()->create();

        $response = $this->delete(route('account-opening-balances.destroy', $accountOpeningBalance));

        $response->assertNoContent();

        $this->assertSoftDeleted($accountOpeningBalance);
    }
}
