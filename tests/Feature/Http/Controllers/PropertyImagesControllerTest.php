<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyImages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyImagesController
 */
final class PropertyImagesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyImages = PropertyImages::factory()->count(3)->create();

        $response = $this->get(route('property-images.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyImagesController::class,
            'store',
            \App\Http\Requests\PropertyImagesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $image_url = $this->faker->word();
        $is_cover = $this->faker->boolean();

        $response = $this->post(route('property-images.store'), [
            'property_id' => $property->id,
            'image_url' => $image_url,
            'is_cover' => $is_cover,
        ]);

        $propertyImages = PropertyImage::query()
            ->where('property_id', $property->id)
            ->where('image_url', $image_url)
            ->where('is_cover', $is_cover)
            ->get();
        $this->assertCount(1, $propertyImages);
        $propertyImage = $propertyImages->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyImage = PropertyImages::factory()->create();

        $response = $this->get(route('property-images.show', $propertyImage));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyImagesController::class,
            'update',
            \App\Http\Requests\PropertyImagesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyImage = PropertyImages::factory()->create();
        $property = Property::factory()->create();
        $image_url = $this->faker->word();
        $is_cover = $this->faker->boolean();

        $response = $this->put(route('property-images.update', $propertyImage), [
            'property_id' => $property->id,
            'image_url' => $image_url,
            'is_cover' => $is_cover,
        ]);

        $propertyImage->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyImage->property_id);
        $this->assertEquals($image_url, $propertyImage->image_url);
        $this->assertEquals($is_cover, $propertyImage->is_cover);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyImage = PropertyImages::factory()->create();
        $propertyImage = PropertyImage::factory()->create();

        $response = $this->delete(route('property-images.destroy', $propertyImage));

        $response->assertNoContent();

        $this->assertModelMissing($propertyImage);
    }
}
