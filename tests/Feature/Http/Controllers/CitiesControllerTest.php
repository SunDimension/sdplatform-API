<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cities;
use App\Models\City;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CitiesController
 */
final class CitiesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $cities = Cities::factory()->count(3)->create();

        $response = $this->get(route('cities.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CitiesController::class,
            'store',
            \App\Http\Requests\CitiesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $state = State::factory()->create();
        $name = $this->faker->name();

        $response = $this->post(route('cities.store'), [
            'state_id' => $state->id,
            'name' => $name,
        ]);

        $cities = City::query()
            ->where('state_id', $state->id)
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $cities);
        $city = $cities->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $city = Cities::factory()->create();

        $response = $this->get(route('cities.show', $city));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CitiesController::class,
            'update',
            \App\Http\Requests\CitiesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $city = Cities::factory()->create();
        $state = State::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('cities.update', $city), [
            'state_id' => $state->id,
            'name' => $name,
        ]);

        $city->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($state->id, $city->state_id);
        $this->assertEquals($name, $city->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $city = Cities::factory()->create();
        $city = City::factory()->create();

        $response = $this->delete(route('cities.destroy', $city));

        $response->assertNoContent();

        $this->assertModelMissing($city);
    }
}
