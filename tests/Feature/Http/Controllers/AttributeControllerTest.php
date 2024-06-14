<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AttributeController
 */
final class AttributeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $attributes = Attribute::factory()->count(3)->create();

        $response = $this->get(route('attributes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AttributeController::class,
            'store',
            \App\Http\Requests\AttributeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('attributes.store'), [
            'name' => $name,
        ]);

        $attributes = Attribute::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $attributes);
        $attribute = $attributes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $attribute = Attribute::factory()->create();

        $response = $this->get(route('attributes.show', $attribute));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AttributeController::class,
            'update',
            \App\Http\Requests\AttributeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $attribute = Attribute::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('attributes.update', $attribute), [
            'name' => $name,
        ]);

        $attribute->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $attribute->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $attribute = Attribute::factory()->create();

        $response = $this->delete(route('attributes.destroy', $attribute));

        $response->assertNoContent();

        $this->assertModelMissing($attribute);
    }
}
