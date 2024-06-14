<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UnitController
 */
final class UnitControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $units = Unit::factory()->count(3)->create();

        $response = $this->get(route('units.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UnitController::class,
            'store',
            \App\Http\Requests\UnitStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('units.store'), [
            'name' => $name,
        ]);

        $units = Unit::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $units);
        $unit = $units->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->get(route('units.show', $unit));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UnitController::class,
            'update',
            \App\Http\Requests\UnitUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $unit = Unit::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('units.update', $unit), [
            'name' => $name,
        ]);

        $unit->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $unit->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.destroy', $unit));

        $response->assertNoContent();

        $this->assertModelMissing($unit);
    }
}
