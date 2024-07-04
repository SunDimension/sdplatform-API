<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChartCategory;
use App\Models\ChartProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChartCategoryController
 */
final class ChartCategoryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $chartCategories = ChartCategory::factory()->count(3)->create();

        $response = $this->get(route('chart-categories.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartCategoryController::class,
            'store',
            \App\Http\Requests\ChartCategoryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $chart_provider = ChartProvider::factory()->create();
        $chart_category = $this->faker->word();

        $response = $this->post(route('chart-categories.store'), [
            'chart_provider_id' => $chart_provider->id,
            'chart_category' => $chart_category,
        ]);

        $chartCategories = ChartCategory::query()
            ->where('chart_provider_id', $chart_provider->id)
            ->where('chart_category', $chart_category)
            ->get();
        $this->assertCount(1, $chartCategories);
        $chartCategory = $chartCategories->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $chartCategory = ChartCategory::factory()->create();

        $response = $this->get(route('chart-categories.show', $chartCategory));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartCategoryController::class,
            'update',
            \App\Http\Requests\ChartCategoryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $chartCategory = ChartCategory::factory()->create();
        $chart_provider = ChartProvider::factory()->create();
        $chart_category = $this->faker->word();

        $response = $this->put(route('chart-categories.update', $chartCategory), [
            'chart_provider_id' => $chart_provider->id,
            'chart_category' => $chart_category,
        ]);

        $chartCategory->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart_provider->id, $chartCategory->chart_provider_id);
        $this->assertEquals($chart_category, $chartCategory->chart_category);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $chartCategory = ChartCategory::factory()->create();

        $response = $this->delete(route('chart-categories.destroy', $chartCategory));

        $response->assertNoContent();

        $this->assertSoftDeleted($chartCategory);
    }
}
