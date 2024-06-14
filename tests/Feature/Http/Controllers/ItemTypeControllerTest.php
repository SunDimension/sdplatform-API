<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ItemTypeController
 */
final class ItemTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $itemTypes = ItemType::factory()->count(3)->create();

        $response = $this->get(route('item-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ItemTypeController::class,
            'store',
            \App\Http\Requests\ItemTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('item-types.store'), [
            'name' => $name,
        ]);

        $itemTypes = ItemType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $itemTypes);
        $itemType = $itemTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $itemType = ItemType::factory()->create();

        $response = $this->get(route('item-types.show', $itemType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ItemTypeController::class,
            'update',
            \App\Http\Requests\ItemTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $itemType = ItemType::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('item-types.update', $itemType), [
            'name' => $name,
        ]);

        $itemType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $itemType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $itemType = ItemType::factory()->create();

        $response = $this->delete(route('item-types.destroy', $itemType));

        $response->assertNoContent();

        $this->assertModelMissing($itemType);
    }
}
