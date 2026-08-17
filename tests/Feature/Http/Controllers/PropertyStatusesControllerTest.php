<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PropertyStatus;
use App\Models\PropertyStatuses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyStatusesController
 */
final class PropertyStatusesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyStatuses = PropertyStatuses::factory()->count(3)->create();

        $response = $this->get(route('property-statuses.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyStatusesController::class,
            'store',
            \App\Http\Requests\PropertyStatusesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('property-statuses.store'), [
            'name' => $name,
        ]);

        $propertyStatuses = PropertyStatus::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $propertyStatuses);
        $propertyStatus = $propertyStatuses->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyStatus = PropertyStatuses::factory()->create();

        $response = $this->get(route('property-statuses.show', $propertyStatus));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyStatusesController::class,
            'update',
            \App\Http\Requests\PropertyStatusesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyStatus = PropertyStatuses::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('property-statuses.update', $propertyStatus), [
            'name' => $name,
        ]);

        $propertyStatus->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $propertyStatus->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyStatus = PropertyStatuses::factory()->create();
        $propertyStatus = PropertyStatus::factory()->create();

        $response = $this->delete(route('property-statuses.destroy', $propertyStatus));

        $response->assertNoContent();

        $this->assertModelMissing($propertyStatus);
    }
}
