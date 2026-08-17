<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyView;
use App\Models\PropertyViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyViewsController
 */
final class PropertyViewsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyViews = PropertyViews::factory()->count(3)->create();

        $response = $this->get(route('property-views.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyViewsController::class,
            'store',
            \App\Http\Requests\PropertyViewsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $viewed_at = Carbon::parse($this->faker->dateTime());

        $response = $this->post(route('property-views.store'), [
            'property_id' => $property->id,
            'viewed_at' => $viewed_at->toDateTimeString(),
        ]);

        $propertyViews = PropertyView::query()
            ->where('property_id', $property->id)
            ->where('viewed_at', $viewed_at)
            ->get();
        $this->assertCount(1, $propertyViews);
        $propertyView = $propertyViews->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyView = PropertyViews::factory()->create();

        $response = $this->get(route('property-views.show', $propertyView));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyViewsController::class,
            'update',
            \App\Http\Requests\PropertyViewsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyView = PropertyViews::factory()->create();
        $property = Property::factory()->create();
        $viewed_at = Carbon::parse($this->faker->dateTime());

        $response = $this->put(route('property-views.update', $propertyView), [
            'property_id' => $property->id,
            'viewed_at' => $viewed_at->toDateTimeString(),
        ]);

        $propertyView->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyView->property_id);
        $this->assertEquals($viewed_at->timestamp, $propertyView->viewed_at);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyView = PropertyViews::factory()->create();
        $propertyView = PropertyView::factory()->create();

        $response = $this->delete(route('property-views.destroy', $propertyView));

        $response->assertNoContent();

        $this->assertModelMissing($propertyView);
    }
}
