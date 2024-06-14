<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Weight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\WeightController
 */
final class WeightControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $weights = Weight::factory()->count(3)->create();

        $response = $this->get(route('weights.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WeightController::class,
            'store',
            \App\Http\Requests\WeightStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('weights.store'), [
            'name' => $name,
        ]);

        $weights = Weight::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $weights);
        $weight = $weights->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $weight = Weight::factory()->create();

        $response = $this->get(route('weights.show', $weight));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WeightController::class,
            'update',
            \App\Http\Requests\WeightUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $weight = Weight::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('weights.update', $weight), [
            'name' => $name,
        ]);

        $weight->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $weight->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $weight = Weight::factory()->create();

        $response = $this->delete(route('weights.destroy', $weight));

        $response->assertNoContent();

        $this->assertModelMissing($weight);
    }
}
