<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalProcessFlow;
use App\Models\ApprovalStage;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use App\Models\ProcessModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalProcessFlowController
 */
final class ApprovalProcessFlowControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalProcessFlows = ApprovalProcessFlow::factory()->count(3)->create();

        $response = $this->get(route('approval-process-flows.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessFlowController::class,
            'store',
            \App\Http\Requests\ApprovalProcessFlowStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $sequence_no = $this->faker->word();
        $process_module = ProcessModule::factory()->create();
        $approval_stage = ApprovalStage::factory()->create();
        $status_id = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-process-flows.store'), [
            'sequence_no' => $sequence_no,
            'process_module_id' => $process_module->id,
            'approval_stage_id' => $approval_stage->id,
            'status_id' => $status_id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessFlows = ApprovalProcessFlow::query()
            ->where('sequence_no', $sequence_no)
            ->where('process_module_id', $process_module->id)
            ->where('approval_stage_id', $approval_stage->id)
            ->where('status_id', $status_id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalProcessFlows);
        $approvalProcessFlow = $approvalProcessFlows->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalProcessFlow = ApprovalProcessFlow::factory()->create();

        $response = $this->get(route('approval-process-flows.show', $approvalProcessFlow));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessFlowController::class,
            'update',
            \App\Http\Requests\ApprovalProcessFlowUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalProcessFlow = ApprovalProcessFlow::factory()->create();
        $sequence_no = $this->faker->word();
        $process_module = ProcessModule::factory()->create();
        $approval_stage = ApprovalStage::factory()->create();
        $status_id = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-process-flows.update', $approvalProcessFlow), [
            'sequence_no' => $sequence_no,
            'process_module_id' => $process_module->id,
            'approval_stage_id' => $approval_stage->id,
            'status_id' => $status_id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessFlow->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($sequence_no, $approvalProcessFlow->sequence_no);
        $this->assertEquals($process_module->id, $approvalProcessFlow->process_module_id);
        $this->assertEquals($approval_stage->id, $approvalProcessFlow->approval_stage_id);
        $this->assertEquals($status_id, $approvalProcessFlow->status_id);
        $this->assertEquals($created_by->id, $approvalProcessFlow->created_by);
        $this->assertEquals($modified_by->id, $approvalProcessFlow->modified_by);
        $this->assertEquals($deleted_by->id, $approvalProcessFlow->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalProcessFlow = ApprovalProcessFlow::factory()->create();

        $response = $this->delete(route('approval-process-flows.destroy', $approvalProcessFlow));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalProcessFlow);
    }
}
