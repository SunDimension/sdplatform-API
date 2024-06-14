<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ItemCategoryController
 */
final class ItemCategoryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $itemCategories = ItemCategory::factory()->count(3)->create();

        $response = $this->get(route('item-categories.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ItemCategoryController::class,
            'store',
            \App\Http\Requests\ItemCategoryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('item-categories.store'), [
            'name' => $name,
        ]);

        $itemCategories = ItemCategory::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $itemCategories);
        $itemCategory = $itemCategories->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $itemCategory = ItemCategory::factory()->create();

        $response = $this->get(route('item-categories.show', $itemCategory));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ItemCategoryController::class,
            'update',
            \App\Http\Requests\ItemCategoryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $itemCategory = ItemCategory::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('item-categories.update', $itemCategory), [
            'name' => $name,
        ]);

        $itemCategory->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $itemCategory->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $itemCategory = ItemCategory::factory()->create();

        $response = $this->delete(route('item-categories.destroy', $itemCategory));

        $response->assertNoContent();

        $this->assertModelMissing($itemCategory);
    }
}
