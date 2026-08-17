<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyAmenities;
use App\Models\PropertyAmenity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyAmenitiesController
 */
final class PropertyAmenitiesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyAmenities = PropertyAmenities::factory()->count(3)->create();

        $response = $this->get(route('property-amenities.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyAmenitiesController::class,
            'store',
            \App\Http\Requests\PropertyAmenitiesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $amenity = Amenity::factory()->create();

        $response = $this->post(route('property-amenities.store'), [
            'property_id' => $property->id,
            'amenity_id' => $amenity->id,
        ]);

        $propertyAmenities = PropertyAmenity::query()
            ->where('property_id', $property->id)
            ->where('amenity_id', $amenity->id)
            ->get();
        $this->assertCount(1, $propertyAmenities);
        $propertyAmenity = $propertyAmenities->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyAmenity = PropertyAmenities::factory()->create();

        $response = $this->get(route('property-amenities.show', $propertyAmenity));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyAmenitiesController::class,
            'update',
            \App\Http\Requests\PropertyAmenitiesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyAmenity = PropertyAmenities::factory()->create();
        $property = Property::factory()->create();
        $amenity = Amenity::factory()->create();

        $response = $this->put(route('property-amenities.update', $propertyAmenity), [
            'property_id' => $property->id,
            'amenity_id' => $amenity->id,
        ]);

        $propertyAmenity->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyAmenity->property_id);
        $this->assertEquals($amenity->id, $propertyAmenity->amenity_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyAmenity = PropertyAmenities::factory()->create();
        $propertyAmenity = PropertyAmenity::factory()->create();

        $response = $this->delete(route('property-amenities.destroy', $propertyAmenity));

        $response->assertNoContent();

        $this->assertModelMissing($propertyAmenity);
    }
}
