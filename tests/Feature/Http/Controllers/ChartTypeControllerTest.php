<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChartCategory;
use App\Models\ChartType;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
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
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('chart-types.store'), [
            'chart_category_id' => $chart_category->id,
            'chart_type' => $chart_type,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $chartTypes = ChartType::query()
            ->where('chart_category_id', $chart_category->id)
            ->where('chart_type', $chart_type)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
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
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('chart-types.update', $chartType), [
            'chart_category_id' => $chart_category->id,
            'chart_type' => $chart_type,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $chartType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart_category->id, $chartType->chart_category_id);
        $this->assertEquals($chart_type, $chartType->chart_type);
        $this->assertEquals($created_by->id, $chartType->created_by);
        $this->assertEquals($modified_by->id, $chartType->modified_by);
        $this->assertEquals($deleted_by->id, $chartType->deleted_by);
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
