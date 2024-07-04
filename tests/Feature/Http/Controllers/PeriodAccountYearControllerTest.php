<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\PeriodAccountYear;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PeriodAccountYearController
 */
final class PeriodAccountYearControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $periodAccountYears = PeriodAccountYear::factory()->count(3)->create();

        $response = $this->get(route('period-account-years.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountYearController::class,
            'store',
            \App\Http\Requests\PeriodAccountYearStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $financial_year = FinancialYear::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account = Account::factory()->create();

        $response = $this->post(route('period-account-years.store'), [
            'financial_year_id' => $financial_year->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_id' => $account->id,
        ]);

        $periodAccountYears = PeriodAccountYear::query()
            ->where('financial_year_id', $financial_year->id)
            ->where('debit', $debit)
            ->where('credit', $credit)
            ->where('amount', $amount)
            ->where('warehouse_id', $warehouse->id)
            ->where('account_id', $account->id)
            ->get();
        $this->assertCount(1, $periodAccountYears);
        $periodAccountYear = $periodAccountYears->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $periodAccountYear = PeriodAccountYear::factory()->create();

        $response = $this->get(route('period-account-years.show', $periodAccountYear));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountYearController::class,
            'update',
            \App\Http\Requests\PeriodAccountYearUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $periodAccountYear = PeriodAccountYear::factory()->create();
        $financial_year = FinancialYear::factory()->create();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account = Account::factory()->create();

        $response = $this->put(route('period-account-years.update', $periodAccountYear), [
            'financial_year_id' => $financial_year->id,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_id' => $account->id,
        ]);

        $periodAccountYear->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($financial_year->id, $periodAccountYear->financial_year_id);
        $this->assertEquals($debit, $periodAccountYear->debit);
        $this->assertEquals($credit, $periodAccountYear->credit);
        $this->assertEquals($amount, $periodAccountYear->amount);
        $this->assertEquals($warehouse->id, $periodAccountYear->warehouse_id);
        $this->assertEquals($account->id, $periodAccountYear->account_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $periodAccountYear = PeriodAccountYear::factory()->create();

        $response = $this->delete(route('period-account-years.destroy', $periodAccountYear));

        $response->assertNoContent();

        $this->assertSoftDeleted($periodAccountYear);
    }
}
