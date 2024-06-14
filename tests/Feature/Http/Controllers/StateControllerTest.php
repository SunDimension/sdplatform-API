<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StateController
 */
final class StateControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $states = State::factory()->count(3)->create();

        $response = $this->get(route('states.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StateController::class,
            'store',
            \App\Http\Requests\StateStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('states.store'), [
            'name' => $name,
        ]);

        $states = State::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $states);
        $state = $states->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $state = State::factory()->create();

        $response = $this->get(route('states.show', $state));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StateController::class,
            'update',
            \App\Http\Requests\StateUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $state = State::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('states.update', $state), [
            'name' => $name,
        ]);

        $state->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $state->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $state = State::factory()->create();

        $response = $this->delete(route('states.destroy', $state));

        $response->assertNoContent();

        $this->assertModelMissing($state);
    }
}
