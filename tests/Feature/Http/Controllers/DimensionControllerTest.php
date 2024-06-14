<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Dimension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DimensionController
 */
final class DimensionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $dimensions = Dimension::factory()->count(3)->create();

        $response = $this->get(route('dimensions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DimensionController::class,
            'store',
            \App\Http\Requests\DimensionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('dimensions.store'), [
            'name' => $name,
        ]);

        $dimensions = Dimension::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $dimensions);
        $dimension = $dimensions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $dimension = Dimension::factory()->create();

        $response = $this->get(route('dimensions.show', $dimension));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DimensionController::class,
            'update',
            \App\Http\Requests\DimensionUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $dimension = Dimension::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('dimensions.update', $dimension), [
            'name' => $name,
        ]);

        $dimension->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $dimension->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $dimension = Dimension::factory()->create();

        $response = $this->delete(route('dimensions.destroy', $dimension));

        $response->assertNoContent();

        $this->assertModelMissing($dimension);
    }
}
