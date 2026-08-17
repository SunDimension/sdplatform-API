<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Amenities;
use App\Models\Amenity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AmenitiesController
 */
final class AmenitiesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $amenities = Amenities::factory()->count(3)->create();

        $response = $this->get(route('amenities.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AmenitiesController::class,
            'store',
            \App\Http\Requests\AmenitiesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('amenities.store'), [
            'name' => $name,
        ]);

        $amenities = Amenity::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $amenities);
        $amenity = $amenities->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $amenity = Amenities::factory()->create();

        $response = $this->get(route('amenities.show', $amenity));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AmenitiesController::class,
            'update',
            \App\Http\Requests\AmenitiesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $amenity = Amenities::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('amenities.update', $amenity), [
            'name' => $name,
        ]);

        $amenity->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $amenity->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $amenity = Amenities::factory()->create();
        $amenity = Amenity::factory()->create();

        $response = $this->delete(route('amenities.destroy', $amenity));

        $response->assertNoContent();

        $this->assertModelMissing($amenity);
    }
}
