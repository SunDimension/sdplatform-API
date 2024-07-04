<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chart;
use App\Models\ChartCategory;
use App\Models\ChartType;
use App\Models\CreatedBy;
use App\Models\DashboardSetting;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DashboardSettingController
 */
final class DashboardSettingControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $dashboardSettings = DashboardSetting::factory()->count(3)->create();

        $response = $this->get(route('dashboard-settings.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DashboardSettingController::class,
            'store',
            \App\Http\Requests\DashboardSettingStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $chart = Chart::factory()->create();
        $module = Module::factory()->create();
        $chart_type = ChartType::factory()->create();
        $chart_category = ChartCategory::factory()->create();
        $chart_title = $this->faker->word();
        $is_active = $this->faker->word();
        $order_by = $this->faker->word();
        $is_group = $this->faker->word();
        $submodule_Id = $this->faker->word();
        $add_condition = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('dashboard-settings.store'), [
            'chart_id' => $chart->id,
            'module_id' => $module->id,
            'chart_type_id' => $chart_type->id,
            'chart_category_id' => $chart_category->id,
            'chart_title' => $chart_title,
            'is_active' => $is_active,
            'order_by' => $order_by,
            'is_group' => $is_group,
            'submodule_Id' => $submodule_Id,
            'add_condition' => $add_condition,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $dashboardSettings = DashboardSetting::query()
            ->where('chart_id', $chart->id)
            ->where('module_id', $module->id)
            ->where('chart_type_id', $chart_type->id)
            ->where('chart_category_id', $chart_category->id)
            ->where('chart_title', $chart_title)
            ->where('is_active', $is_active)
            ->where('order_by', $order_by)
            ->where('is_group', $is_group)
            ->where('submodule_Id', $submodule_Id)
            ->where('add_condition', $add_condition)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $dashboardSettings);
        $dashboardSetting = $dashboardSettings->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $dashboardSetting = DashboardSetting::factory()->create();

        $response = $this->get(route('dashboard-settings.show', $dashboardSetting));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DashboardSettingController::class,
            'update',
            \App\Http\Requests\DashboardSettingUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $dashboardSetting = DashboardSetting::factory()->create();
        $chart = Chart::factory()->create();
        $module = Module::factory()->create();
        $chart_type = ChartType::factory()->create();
        $chart_category = ChartCategory::factory()->create();
        $chart_title = $this->faker->word();
        $is_active = $this->faker->word();
        $order_by = $this->faker->word();
        $is_group = $this->faker->word();
        $submodule_Id = $this->faker->word();
        $add_condition = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('dashboard-settings.update', $dashboardSetting), [
            'chart_id' => $chart->id,
            'module_id' => $module->id,
            'chart_type_id' => $chart_type->id,
            'chart_category_id' => $chart_category->id,
            'chart_title' => $chart_title,
            'is_active' => $is_active,
            'order_by' => $order_by,
            'is_group' => $is_group,
            'submodule_Id' => $submodule_Id,
            'add_condition' => $add_condition,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $dashboardSetting->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($chart->id, $dashboardSetting->chart_id);
        $this->assertEquals($module->id, $dashboardSetting->module_id);
        $this->assertEquals($chart_type->id, $dashboardSetting->chart_type_id);
        $this->assertEquals($chart_category->id, $dashboardSetting->chart_category_id);
        $this->assertEquals($chart_title, $dashboardSetting->chart_title);
        $this->assertEquals($is_active, $dashboardSetting->is_active);
        $this->assertEquals($order_by, $dashboardSetting->order_by);
        $this->assertEquals($is_group, $dashboardSetting->is_group);
        $this->assertEquals($submodule_Id, $dashboardSetting->submodule_Id);
        $this->assertEquals($add_condition, $dashboardSetting->add_condition);
        $this->assertEquals($created_by->id, $dashboardSetting->created_by);
        $this->assertEquals($modified_by->id, $dashboardSetting->modified_by);
        $this->assertEquals($deleted_by->id, $dashboardSetting->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $dashboardSetting = DashboardSetting::factory()->create();

        $response = $this->delete(route('dashboard-settings.destroy', $dashboardSetting));

        $response->assertNoContent();

        $this->assertSoftDeleted($dashboardSetting);
    }
}
