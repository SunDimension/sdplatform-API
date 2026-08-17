<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Area;
use App\Models\Areas;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AreasController
 */
final class AreasControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $areas = Areas::factory()->count(3)->create();

        $response = $this->get(route('areas.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreasController::class,
            'store',
            \App\Http\Requests\AreasStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $city = City::factory()->create();
        $name = $this->faker->name();

        $response = $this->post(route('areas.store'), [
            'city_id' => $city->id,
            'name' => $name,
        ]);

        $areas = Area::query()
            ->where('city_id', $city->id)
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $areas);
        $area = $areas->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $area = Areas::factory()->create();

        $response = $this->get(route('areas.show', $area));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AreasController::class,
            'update',
            \App\Http\Requests\AreasUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $area = Areas::factory()->create();
        $city = City::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('areas.update', $area), [
            'city_id' => $city->id,
            'name' => $name,
        ]);

        $area->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($city->id, $area->city_id);
        $this->assertEquals($name, $area->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $area = Areas::factory()->create();
        $area = Area::factory()->create();

        $response = $this->delete(route('areas.destroy', $area));

        $response->assertNoContent();

        $this->assertModelMissing($area);
    }
}
