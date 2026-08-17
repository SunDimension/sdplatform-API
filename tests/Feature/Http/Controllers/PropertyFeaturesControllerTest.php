<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Feature;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyFeatures;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyFeaturesController
 */
final class PropertyFeaturesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyFeatures = PropertyFeatures::factory()->count(3)->create();

        $response = $this->get(route('property-features.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyFeaturesController::class,
            'store',
            \App\Http\Requests\PropertyFeaturesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $feature = Feature::factory()->create();

        $response = $this->post(route('property-features.store'), [
            'property_id' => $property->id,
            'feature_id' => $feature->id,
        ]);

        $propertyFeatures = PropertyFeature::query()
            ->where('property_id', $property->id)
            ->where('feature_id', $feature->id)
            ->get();
        $this->assertCount(1, $propertyFeatures);
        $propertyFeature = $propertyFeatures->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyFeature = PropertyFeatures::factory()->create();

        $response = $this->get(route('property-features.show', $propertyFeature));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyFeaturesController::class,
            'update',
            \App\Http\Requests\PropertyFeaturesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyFeature = PropertyFeatures::factory()->create();
        $property = Property::factory()->create();
        $feature = Feature::factory()->create();

        $response = $this->put(route('property-features.update', $propertyFeature), [
            'property_id' => $property->id,
            'feature_id' => $feature->id,
        ]);

        $propertyFeature->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyFeature->property_id);
        $this->assertEquals($feature->id, $propertyFeature->feature_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyFeature = PropertyFeatures::factory()->create();
        $propertyFeature = PropertyFeature::factory()->create();

        $response = $this->delete(route('property-features.destroy', $propertyFeature));

        $response->assertNoContent();

        $this->assertModelMissing($propertyFeature);
    }
}
