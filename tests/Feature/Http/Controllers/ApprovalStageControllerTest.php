<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalStage;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use App\Models\ProcessType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalStageController
 */
final class ApprovalStageControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalStages = ApprovalStage::factory()->count(3)->create();

        $response = $this->get(route('approval-stages.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalStageController::class,
            'store',
            \App\Http\Requests\ApprovalStageStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $process_type = ProcessType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-stages.store'), [
            'name' => $name,
            'process_type_id' => $process_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalStages = ApprovalStage::query()
            ->where('name', $name)
            ->where('process_type_id', $process_type->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalStages);
        $approvalStage = $approvalStages->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalStage = ApprovalStage::factory()->create();

        $response = $this->get(route('approval-stages.show', $approvalStage));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalStageController::class,
            'update',
            \App\Http\Requests\ApprovalStageUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalStage = ApprovalStage::factory()->create();
        $name = $this->faker->name();
        $process_type = ProcessType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-stages.update', $approvalStage), [
            'name' => $name,
            'process_type_id' => $process_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalStage->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $approvalStage->name);
        $this->assertEquals($process_type->id, $approvalStage->process_type_id);
        $this->assertEquals($created_by->id, $approvalStage->created_by);
        $this->assertEquals($modified_by->id, $approvalStage->modified_by);
        $this->assertEquals($deleted_by->id, $approvalStage->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalStage = ApprovalStage::factory()->create();

        $response = $this->delete(route('approval-stages.destroy', $approvalStage));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalStage);
    }
}
