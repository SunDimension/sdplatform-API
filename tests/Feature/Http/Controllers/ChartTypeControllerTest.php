<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChartCategory;
use App\Models\ChartType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChartTypeController
 */
final class ChartTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $chartTypes = ChartType::factory()->count(3)->create();

        $response = $this->get(route('chart-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartTypeController::class,
            'store',
            \App\Http\Requests\ChartTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $chart_category = ChartCategory::factory()->create();
        $chart_type = $this->faker->word();

        $response = $this->post(route('chart-types.store'), [
            'chart_category_id' => $chart_category->id,
            'chart_type' => $chart_type,
        ]);

        $chartTypes = ChartType::query()
            ->where('chart_category_id', $chart_category->id)
            ->where('chart_type', $chart_type)
            ->get();
        $this->assertCount(1, $chartTypes);
        $chartType = $chartTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $chartType = ChartType::factory()->create();

        $response = $this->get(route('chart-types.show', $chartType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartTypeController::class,
            'update',
            \App\Http\Requests\ChartTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $chartType = ChartType::factory()->create();
        $chart_category = ChartCategory::factory()->create();
        $chart_type = $this->faker->word();

        $response = $this->put(route('chart-types.update', $chartType), [
            'chart_category_id' => $chart_category->id,
            'chart_type' => $chart_type,
        ]);

        $chartType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart_category->id, $chartType->chart_category_id);
        $this->assertEquals($chart_type, $chartType->chart_type);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $chartType = ChartType::factory()->create();

        $response = $this->delete(route('chart-types.destroy', $chartType));

        $response->assertNoContent();

        $this->assertSoftDeleted($chartType);
    }
}
