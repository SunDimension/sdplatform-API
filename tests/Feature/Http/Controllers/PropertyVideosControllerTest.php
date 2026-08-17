<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyVideo;
use App\Models\PropertyVideos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyVideosController
 */
final class PropertyVideosControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyVideos = PropertyVideos::factory()->count(3)->create();

        $response = $this->get(route('property-videos.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyVideosController::class,
            'store',
            \App\Http\Requests\PropertyVideosStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $video_url = $this->faker->word();

        $response = $this->post(route('property-videos.store'), [
            'property_id' => $property->id,
            'video_url' => $video_url,
        ]);

        $propertyVideos = PropertyVideo::query()
            ->where('property_id', $property->id)
            ->where('video_url', $video_url)
            ->get();
        $this->assertCount(1, $propertyVideos);
        $propertyVideo = $propertyVideos->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyVideo = PropertyVideos::factory()->create();

        $response = $this->get(route('property-videos.show', $propertyVideo));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyVideosController::class,
            'update',
            \App\Http\Requests\PropertyVideosUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyVideo = PropertyVideos::factory()->create();
        $property = Property::factory()->create();
        $video_url = $this->faker->word();

        $response = $this->put(route('property-videos.update', $propertyVideo), [
            'property_id' => $property->id,
            'video_url' => $video_url,
        ]);

        $propertyVideo->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyVideo->property_id);
        $this->assertEquals($video_url, $propertyVideo->video_url);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyVideo = PropertyVideos::factory()->create();
        $propertyVideo = PropertyVideo::factory()->create();

        $response = $this->delete(route('property-videos.destroy', $propertyVideo));

        $response->assertNoContent();

        $this->assertModelMissing($propertyVideo);
    }
}
