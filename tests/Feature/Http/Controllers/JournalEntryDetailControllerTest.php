<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Foreign;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\JournalType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\JournalEntryDetailController
 */
final class JournalEntryDetailControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $journalEntryDetails = JournalEntryDetail::factory()->count(3)->create();

        $response = $this->get(route('journal-entry-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\JournalEntryDetailController::class,
            'store',
            \App\Http\Requests\JournalEntryDetailStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $journal_entry = JournalEntry::factory()->create();
        $journal_type = JournalType::factory()->create();
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $description = $this->faker->text();
        $account = Foreign::factory()->create();
        $account_no = $this->faker->word();

        $response = $this->post(route('journal-entry-details.store'), [
            'journal_entry_id' => $journal_entry->id,
            'journal_type_id' => $journal_type->id,
            'amount' => $amount,
            'description' => $description,
            'account_id' => $account->id,
            'account_no' => $account_no,
        ]);

        $journalEntryDetails = JournalEntryDetail::query()
            ->where('journal_entry_id', $journal_entry->id)
            ->where('journal_type_id', $journal_type->id)
            ->where('amount', $amount)
            ->where('description', $description)
            ->where('account_id', $account->id)
            ->where('account_no', $account_no)
            ->get();
        $this->assertCount(1, $journalEntryDetails);
        $journalEntryDetail = $journalEntryDetails->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $journalEntryDetail = JournalEntryDetail::factory()->create();

        $response = $this->get(route('journal-entry-details.show', $journalEntryDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\JournalEntryDetailController::class,
            'update',
            \App\Http\Requests\JournalEntryDetailUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $journalEntryDetail = JournalEntryDetail::factory()->create();
        $journal_entry = JournalEntry::factory()->create();
        $journal_type = JournalType::factory()->create();
        $amount = $this->faker->randomFloat(/** double_attributes **/);
        $description = $this->faker->text();
        $account = Foreign::factory()->create();
        $account_no = $this->faker->word();

        $response = $this->put(route('journal-entry-details.update', $journalEntryDetail), [
            'journal_entry_id' => $journal_entry->id,
            'journal_type_id' => $journal_type->id,
            'amount' => $amount,
            'description' => $description,
            'account_id' => $account->id,
            'account_no' => $account_no,
        ]);

        $journalEntryDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($journal_entry->id, $journalEntryDetail->journal_entry_id);
        $this->assertEquals($journal_type->id, $journalEntryDetail->journal_type_id);
        $this->assertEquals($amount, $journalEntryDetail->amount);
        $this->assertEquals($description, $journalEntryDetail->description);
        $this->assertEquals($account->id, $journalEntryDetail->account_id);
        $this->assertEquals($account_no, $journalEntryDetail->account_no);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $journalEntryDetail = JournalEntryDetail::factory()->create();

        $response = $this->delete(route('journal-entry-details.destroy', $journalEntryDetail));

        $response->assertNoContent();

        $this->assertSoftDeleted($journalEntryDetail);
    }
}
