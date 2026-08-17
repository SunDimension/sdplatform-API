<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Feature;
use App\Models\Features;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FeaturesController
 */
final class FeaturesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $features = Features::factory()->count(3)->create();

        $response = $this->get(route('features.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FeaturesController::class,
            'store',
            \App\Http\Requests\FeaturesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('features.store'), [
            'name' => $name,
        ]);

        $features = Feature::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $features);
        $feature = $features->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $feature = Features::factory()->create();

        $response = $this->get(route('features.show', $feature));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FeaturesController::class,
            'update',
            \App\Http\Requests\FeaturesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $feature = Features::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('features.update', $feature), [
            'name' => $name,
        ]);

        $feature->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $feature->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $feature = Features::factory()->create();
        $feature = Feature::factory()->create();

        $response = $this->delete(route('features.destroy', $feature));

        $response->assertNoContent();

        $this->assertModelMissing($feature);
    }
}
