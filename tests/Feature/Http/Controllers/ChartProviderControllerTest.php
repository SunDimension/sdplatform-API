<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChartProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChartProviderController
 */
final class ChartProviderControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $chartProviders = ChartProvider::factory()->count(3)->create();

        $response = $this->get(route('chart-providers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartProviderController::class,
            'store',
            \App\Http\Requests\ChartProviderStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $chart_provider = $this->faker->word();

        $response = $this->post(route('chart-providers.store'), [
            'chart_provider' => $chart_provider,
        ]);

        $chartProviders = ChartProvider::query()
            ->where('chart_provider', $chart_provider)
            ->get();
        $this->assertCount(1, $chartProviders);
        $chartProvider = $chartProviders->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $chartProvider = ChartProvider::factory()->create();

        $response = $this->get(route('chart-providers.show', $chartProvider));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartProviderController::class,
            'update',
            \App\Http\Requests\ChartProviderUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $chartProvider = ChartProvider::factory()->create();
        $chart_provider = $this->faker->word();

        $response = $this->put(route('chart-providers.update', $chartProvider), [
            'chart_provider' => $chart_provider,
        ]);

        $chartProvider->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart_provider, $chartProvider->chart_provider);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $chartProvider = ChartProvider::factory()->create();

        $response = $this->delete(route('chart-providers.destroy', $chartProvider));

        $response->assertNoContent();

        $this->assertSoftDeleted($chartProvider);
    }
}
