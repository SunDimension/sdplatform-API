<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\ReportedListing;
use App\Models\ReportedListings;
use App\Models\Reporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReportedListingsController
 */
final class ReportedListingsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $reportedListings = ReportedListings::factory()->count(3)->create();

        $response = $this->get(route('reported-listings.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReportedListingsController::class,
            'store',
            \App\Http\Requests\ReportedListingsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $reporter = Reporter::factory()->create();
        $reason = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->post(route('reported-listings.store'), [
            'property_id' => $property->id,
            'reporter_id' => $reporter->id,
            'reason' => $reason,
            'status' => $status,
        ]);

        $reportedListings = ReportedListing::query()
            ->where('property_id', $property->id)
            ->where('reporter_id', $reporter->id)
            ->where('reason', $reason)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $reportedListings);
        $reportedListing = $reportedListings->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $reportedListing = ReportedListings::factory()->create();

        $response = $this->get(route('reported-listings.show', $reportedListing));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReportedListingsController::class,
            'update',
            \App\Http\Requests\ReportedListingsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $reportedListing = ReportedListings::factory()->create();
        $property = Property::factory()->create();
        $reporter = Reporter::factory()->create();
        $reason = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->put(route('reported-listings.update', $reportedListing), [
            'property_id' => $property->id,
            'reporter_id' => $reporter->id,
            'reason' => $reason,
            'status' => $status,
        ]);

        $reportedListing->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $reportedListing->property_id);
        $this->assertEquals($reporter->id, $reportedListing->reporter_id);
        $this->assertEquals($reason, $reportedListing->reason);
        $this->assertEquals($status, $reportedListing->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $reportedListing = ReportedListings::factory()->create();
        $reportedListing = ReportedListing::factory()->create();

        $response = $this->delete(route('reported-listings.destroy', $reportedListing));

        $response->assertNoContent();

        $this->assertModelMissing($reportedListing);
    }
}
