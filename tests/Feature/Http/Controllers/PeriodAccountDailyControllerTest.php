<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\PeriodAccountDaily;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PeriodAccountDailyController
 */
final class PeriodAccountDailyControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $periodAccountDailies = PeriodAccountDaily::factory()->count(3)->create();

        $response = $this->get(route('period-account-dailies.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountDailyController::class,
            'store',
            \App\Http\Requests\PeriodAccountDailyStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $period_date = Carbon::parse($this->faker->date());
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();

        $response = $this->post(route('period-account-dailies.store'), [
            'period_date' => $period_date->toDateString(),
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
        ]);

        $periodAccountDailies = PeriodAccountDaily::query()
            ->where('period_date', $period_date)
            ->where('debit', $debit)
            ->where('credit', $credit)
            ->where('amount', $amount)
            ->where('warehouse_id', $warehouse->id)
            ->where('account_no', $account_no)
            ->where('account_id', $account->id)
            ->get();
        $this->assertCount(1, $periodAccountDailies);
        $periodAccountDaily = $periodAccountDailies->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $periodAccountDaily = PeriodAccountDaily::factory()->create();

        $response = $this->get(route('period-account-dailies.show', $periodAccountDaily));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PeriodAccountDailyController::class,
            'update',
            \App\Http\Requests\PeriodAccountDailyUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $periodAccountDaily = PeriodAccountDaily::factory()->create();
        $period_date = Carbon::parse($this->faker->date());
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();

        $response = $this->put(route('period-account-dailies.update', $periodAccountDaily), [
            'period_date' => $period_date->toDateString(),
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
        ]);

        $periodAccountDaily->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($period_date, $periodAccountDaily->period_date);
        $this->assertEquals($debit, $periodAccountDaily->debit);
        $this->assertEquals($credit, $periodAccountDaily->credit);
        $this->assertEquals($amount, $periodAccountDaily->amount);
        $this->assertEquals($warehouse->id, $periodAccountDaily->warehouse_id);
        $this->assertEquals($account_no, $periodAccountDaily->account_no);
        $this->assertEquals($account->id, $periodAccountDaily->account_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $periodAccountDaily = PeriodAccountDaily::factory()->create();

        $response = $this->delete(route('period-account-dailies.destroy', $periodAccountDaily));

        $response->assertNoContent();

        $this->assertSoftDeleted($periodAccountDaily);
    }
}
