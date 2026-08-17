<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PropertyType;
use App\Models\PropertyTypes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyTypesController
 */
final class PropertyTypesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyTypes = PropertyTypes::factory()->count(3)->create();

        $response = $this->get(route('property-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyTypesController::class,
            'store',
            \App\Http\Requests\PropertyTypesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('property-types.store'), [
            'name' => $name,
        ]);

        $propertyTypes = PropertyType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $propertyTypes);
        $propertyType = $propertyTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyType = PropertyTypes::factory()->create();

        $response = $this->get(route('property-types.show', $propertyType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyTypesController::class,
            'update',
            \App\Http\Requests\PropertyTypesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyType = PropertyTypes::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('property-types.update', $propertyType), [
            'name' => $name,
        ]);

        $propertyType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $propertyType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyType = PropertyTypes::factory()->create();
        $propertyType = PropertyType::factory()->create();

        $response = $this->delete(route('property-types.destroy', $propertyType));

        $response->assertNoContent();

        $this->assertModelMissing($propertyType);
    }
}
