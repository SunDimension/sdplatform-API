<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalInstance;
use App\Models\ApprovalStage;
use App\Models\ApprovalType;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalInstanceController
 */
final class ApprovalInstanceControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalInstances = ApprovalInstance::factory()->count(3)->create();

        $response = $this->get(route('approval-instances.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalInstanceController::class,
            'store',
            \App\Http\Requests\ApprovalInstanceStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $approval_stage = ApprovalStage::factory()->create();
        $approval_type = ApprovalType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-instances.store'), [
            'approval_stage_id' => $approval_stage->id,
            'approval_type_id' => $approval_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalInstances = ApprovalInstance::query()
            ->where('approval_stage_id', $approval_stage->id)
            ->where('approval_type_id', $approval_type->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalInstances);
        $approvalInstance = $approvalInstances->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalInstance = ApprovalInstance::factory()->create();

        $response = $this->get(route('approval-instances.show', $approvalInstance));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalInstanceController::class,
            'update',
            \App\Http\Requests\ApprovalInstanceUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalInstance = ApprovalInstance::factory()->create();
        $approval_stage = ApprovalStage::factory()->create();
        $approval_type = ApprovalType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-instances.update', $approvalInstance), [
            'approval_stage_id' => $approval_stage->id,
            'approval_type_id' => $approval_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalInstance->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($approval_stage->id, $approvalInstance->approval_stage_id);
        $this->assertEquals($approval_type->id, $approvalInstance->approval_type_id);
        $this->assertEquals($created_by->id, $approvalInstance->created_by);
        $this->assertEquals($modified_by->id, $approvalInstance->modified_by);
        $this->assertEquals($deleted_by->id, $approvalInstance->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalInstance = ApprovalInstance::factory()->create();

        $response = $this->delete(route('approval-instances.destroy', $approvalInstance));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalInstance);
    }
}
