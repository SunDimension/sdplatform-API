<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\JournalEntryController
 */
final class JournalEntryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $journalEntries = JournalEntry::factory()->count(3)->create();

        $response = $this->get(route('journal-entries.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\JournalEntryController::class,
            'store',
            \App\Http\Requests\JournalEntryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $description = $this->faker->text();
        $payment_date = Carbon::parse($this->faker->dateTime());
        $warehouse = Warehouse::factory()->create();
        $vendor_id = $this->faker->word();

        $response = $this->post(route('journal-entries.store'), [
            'description' => $description,
            'payment_date' => $payment_date->toDateTimeString(),
            'warehouse_id' => $warehouse->id,
            'vendor_id' => $vendor_id,
        ]);

        $journalEntries = JournalEntry::query()
            ->where('description', $description)
            ->where('payment_date', $payment_date)
            ->where('warehouse_id', $warehouse->id)
            ->where('vendor_id', $vendor_id)
            ->get();
        $this->assertCount(1, $journalEntries);
        $journalEntry = $journalEntries->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $journalEntry = JournalEntry::factory()->create();

        $response = $this->get(route('journal-entries.show', $journalEntry));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\JournalEntryController::class,
            'update',
            \App\Http\Requests\JournalEntryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $journalEntry = JournalEntry::factory()->create();
        $description = $this->faker->text();
        $payment_date = Carbon::parse($this->faker->dateTime());
        $warehouse = Warehouse::factory()->create();
        $vendor_id = $this->faker->word();

        $response = $this->put(route('journal-entries.update', $journalEntry), [
            'description' => $description,
            'payment_date' => $payment_date->toDateTimeString(),
            'warehouse_id' => $warehouse->id,
            'vendor_id' => $vendor_id,
        ]);

        $journalEntry->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($description, $journalEntry->description);
        $this->assertEquals($payment_date->timestamp, $journalEntry->payment_date);
        $this->assertEquals($warehouse->id, $journalEntry->warehouse_id);
        $this->assertEquals($vendor_id, $journalEntry->vendor_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $journalEntry = JournalEntry::factory()->create();

        $response = $this->delete(route('journal-entries.destroy', $journalEntry));

        $response->assertNoContent();

        $this->assertSoftDeleted($journalEntry);
    }
}
