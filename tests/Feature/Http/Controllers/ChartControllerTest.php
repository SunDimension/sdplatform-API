<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chart;
use App\Models\ChartCategory;
use App\Models\ChartType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChartController
 */
final class ChartControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $charts = Chart::factory()->count(3)->create();

        $response = $this->get(route('charts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartController::class,
            'store',
            \App\Http\Requests\ChartStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $chart_title = $this->faker->word();
        $chart_type = ChartType::factory()->create();
        $chart_category = ChartCategory::factory()->create();
        $sql_query = $this->faker->text();
        $is_active = $this->faker->word();
        $module_id = $this->faker->word();
        $filterColumn = $this->faker->word();

        $response = $this->post(route('charts.store'), [
            'chart_title' => $chart_title,
            'chart_type_id' => $chart_type->id,
            'chart_category_id' => $chart_category->id,
            'sql_query' => $sql_query,
            'is_active' => $is_active,
            'module_id' => $module_id,
            'filterColumn' => $filterColumn,
        ]);

        $charts = Chart::query()
            ->where('chart_title', $chart_title)
            ->where('chart_type_id', $chart_type->id)
            ->where('chart_category_id', $chart_category->id)
            ->where('sql_query', $sql_query)
            ->where('is_active', $is_active)
            ->where('module_id', $module_id)
            ->where('filterColumn', $filterColumn)
            ->get();
        $this->assertCount(1, $charts);
        $chart = $charts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $chart = Chart::factory()->create();

        $response = $this->get(route('charts.show', $chart));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartController::class,
            'update',
            \App\Http\Requests\ChartUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $chart = Chart::factory()->create();
        $chart_title = $this->faker->word();
        $chart_type = ChartType::factory()->create();
        $chart_category = ChartCategory::factory()->create();
        $sql_query = $this->faker->text();
        $is_active = $this->faker->word();
        $module_id = $this->faker->word();
        $filterColumn = $this->faker->word();

        $response = $this->put(route('charts.update', $chart), [
            'chart_title' => $chart_title,
            'chart_type_id' => $chart_type->id,
            'chart_category_id' => $chart_category->id,
            'sql_query' => $sql_query,
            'is_active' => $is_active,
            'module_id' => $module_id,
            'filterColumn' => $filterColumn,
        ]);

        $chart->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart_title, $chart->chart_title);
        $this->assertEquals($chart_type->id, $chart->chart_type_id);
        $this->assertEquals($chart_category->id, $chart->chart_category_id);
        $this->assertEquals($sql_query, $chart->sql_query);
        $this->assertEquals($is_active, $chart->is_active);
        $this->assertEquals($module_id, $chart->module_id);
        $this->assertEquals($filterColumn, $chart->filterColumn);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $chart = Chart::factory()->create();

        $response = $this->delete(route('charts.destroy', $chart));

        $response->assertNoContent();

        $this->assertSoftDeleted($chart);
    }
}
