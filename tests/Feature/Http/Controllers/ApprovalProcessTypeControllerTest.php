<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalProcessType;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalProcessTypeController
 */
final class ApprovalProcessTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalProcessTypes = ApprovalProcessType::factory()->count(3)->create();

        $response = $this->get(route('approval-process-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessTypeController::class,
            'store',
            \App\Http\Requests\ApprovalProcessTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-process-types.store'), [
            'name' => $name,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessTypes = ApprovalProcessType::query()
            ->where('name', $name)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalProcessTypes);
        $approvalProcessType = $approvalProcessTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalProcessType = ApprovalProcessType::factory()->create();

        $response = $this->get(route('approval-process-types.show', $approvalProcessType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessTypeController::class,
            'update',
            \App\Http\Requests\ApprovalProcessTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalProcessType = ApprovalProcessType::factory()->create();
        $name = $this->faker->name();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-process-types.update', $approvalProcessType), [
            'name' => $name,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $approvalProcessType->name);
        $this->assertEquals($created_by->id, $approvalProcessType->created_by);
        $this->assertEquals($modified_by->id, $approvalProcessType->modified_by);
        $this->assertEquals($deleted_by->id, $approvalProcessType->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalProcessType = ApprovalProcessType::factory()->create();

        $response = $this->delete(route('approval-process-types.destroy', $approvalProcessType));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalProcessType);
    }
}
