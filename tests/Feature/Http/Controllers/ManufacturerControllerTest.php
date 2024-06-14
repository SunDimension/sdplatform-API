<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ManufacturerController
 */
final class ManufacturerControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $manufacturers = Manufacturer::factory()->count(3)->create();

        $response = $this->get(route('manufacturers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ManufacturerController::class,
            'store',
            \App\Http\Requests\ManufacturerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('manufacturers.store'), [
            'name' => $name,
        ]);

        $manufacturers = Manufacturer::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $manufacturers);
        $manufacturer = $manufacturers->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $manufacturer = Manufacturer::factory()->create();

        $response = $this->get(route('manufacturers.show', $manufacturer));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ManufacturerController::class,
            'update',
            \App\Http\Requests\ManufacturerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('manufacturers.update', $manufacturer), [
            'name' => $name,
        ]);

        $manufacturer->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $manufacturer->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $manufacturer = Manufacturer::factory()->create();

        $response = $this->delete(route('manufacturers.destroy', $manufacturer));

        $response->assertNoContent();

        $this->assertModelMissing($manufacturer);
    }
}
