<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalProcessModule;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalProcessModuleController
 */
final class ApprovalProcessModuleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalProcessModules = ApprovalProcessModule::factory()->count(3)->create();

        $response = $this->get(route('approval-process-modules.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessModuleController::class,
            'store',
            \App\Http\Requests\ApprovalProcessModuleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $max_approval_count = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-process-modules.store'), [
            'name' => $name,
            'max_approval_count' => $max_approval_count,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessModules = ApprovalProcessModule::query()
            ->where('name', $name)
            ->where('max_approval_count', $max_approval_count)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalProcessModules);
        $approvalProcessModule = $approvalProcessModules->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalProcessModule = ApprovalProcessModule::factory()->create();

        $response = $this->get(route('approval-process-modules.show', $approvalProcessModule));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalProcessModuleController::class,
            'update',
            \App\Http\Requests\ApprovalProcessModuleUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalProcessModule = ApprovalProcessModule::factory()->create();
        $name = $this->faker->name();
        $max_approval_count = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-process-modules.update', $approvalProcessModule), [
            'name' => $name,
            'max_approval_count' => $max_approval_count,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalProcessModule->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $approvalProcessModule->name);
        $this->assertEquals($max_approval_count, $approvalProcessModule->max_approval_count);
        $this->assertEquals($created_by->id, $approvalProcessModule->created_by);
        $this->assertEquals($modified_by->id, $approvalProcessModule->modified_by);
        $this->assertEquals($deleted_by->id, $approvalProcessModule->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalProcessModule = ApprovalProcessModule::factory()->create();

        $response = $this->delete(route('approval-process-modules.destroy', $approvalProcessModule));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalProcessModule);
    }
}
