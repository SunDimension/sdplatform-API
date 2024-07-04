<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\FinancialPeriod;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TransactionController
 */
final class TransactionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $transactions = Transaction::factory()->count(3)->create();

        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransactionController::class,
            'store',
            \App\Http\Requests\TransactionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $financial_period = FinancialPeriod::factory()->create();
        $transaction_date = Carbon::parse($this->faker->date());
        $transcode = $this->faker->word();
        $transtype = $this->faker->word();
        $naration = $this->faker->word();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();

        $response = $this->post(route('transactions.store'), [
            'financial_period_id' => $financial_period->id,
            'transaction_date' => $transaction_date->toDateString(),
            'transcode' => $transcode,
            'transtype' => $transtype,
            'naration' => $naration,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
        ]);

        $transactions = Transaction::query()
            ->where('financial_period_id', $financial_period->id)
            ->where('transaction_date', $transaction_date)
            ->where('transcode', $transcode)
            ->where('transtype', $transtype)
            ->where('naration', $naration)
            ->where('debit', $debit)
            ->where('credit', $credit)
            ->where('amount', $amount)
            ->where('warehouse_id', $warehouse->id)
            ->where('account_no', $account_no)
            ->where('account_id', $account->id)
            ->get();
        $this->assertCount(1, $transactions);
        $transaction = $transactions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('transactions.show', $transaction));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TransactionController::class,
            'update',
            \App\Http\Requests\TransactionUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $transaction = Transaction::factory()->create();
        $financial_period = FinancialPeriod::factory()->create();
        $transaction_date = Carbon::parse($this->faker->date());
        $transcode = $this->faker->word();
        $transtype = $this->faker->word();
        $naration = $this->faker->word();
        $debit = $this->faker->randomFloat(/** double_attributes **/);
        $credit = $this->faker->randomFloat(/** double_attributes **/);
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $warehouse = Warehouse::factory()->create();
        $account_no = $this->faker->word();
        $account = Account::factory()->create();

        $response = $this->put(route('transactions.update', $transaction), [
            'financial_period_id' => $financial_period->id,
            'transaction_date' => $transaction_date->toDateString(),
            'transcode' => $transcode,
            'transtype' => $transtype,
            'naration' => $naration,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'warehouse_id' => $warehouse->id,
            'account_no' => $account_no,
            'account_id' => $account->id,
        ]);

        $transaction->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($financial_period->id, $transaction->financial_period_id);
        $this->assertEquals($transaction_date, $transaction->transaction_date);
        $this->assertEquals($transcode, $transaction->transcode);
        $this->assertEquals($transtype, $transaction->transtype);
        $this->assertEquals($naration, $transaction->naration);
        $this->assertEquals($debit, $transaction->debit);
        $this->assertEquals($credit, $transaction->credit);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals($warehouse->id, $transaction->warehouse_id);
        $this->assertEquals($account_no, $transaction->account_no);
        $this->assertEquals($account->id, $transaction->account_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->delete(route('transactions.destroy', $transaction));

        $response->assertNoContent();

        $this->assertSoftDeleted($transaction);
    }
}
