<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PropertyCategories;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyCategoriesController
 */
final class PropertyCategoriesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyCategories = PropertyCategories::factory()->count(3)->create();

        $response = $this->get(route('property-categories.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyCategoriesController::class,
            'store',
            \App\Http\Requests\PropertyCategoriesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('property-categories.store'), [
            'name' => $name,
        ]);

        $propertyCategories = PropertyCategory::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $propertyCategories);
        $propertyCategory = $propertyCategories->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyCategory = PropertyCategories::factory()->create();

        $response = $this->get(route('property-categories.show', $propertyCategory));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyCategoriesController::class,
            'update',
            \App\Http\Requests\PropertyCategoriesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyCategory = PropertyCategories::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('property-categories.update', $propertyCategory), [
            'name' => $name,
        ]);

        $propertyCategory->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $propertyCategory->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyCategory = PropertyCategories::factory()->create();
        $propertyCategory = PropertyCategory::factory()->create();

        $response = $this->delete(route('property-categories.destroy', $propertyCategory));

        $response->assertNoContent();

        $this->assertModelMissing($propertyCategory);
    }
}
