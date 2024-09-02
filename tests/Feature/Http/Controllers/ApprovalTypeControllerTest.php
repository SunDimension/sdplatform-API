<?php

namespace Tests\Feature\Http\Controllers;

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
 * @see \App\Http\Controllers\ApprovalTypeController
 */
final class ApprovalTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalTypes = ApprovalType::factory()->count(3)->create();

        $response = $this->get(route('approval-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalTypeController::class,
            'store',
            \App\Http\Requests\ApprovalTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-types.store'), [
            'name' => $name,
            'description' => $description,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalTypes = ApprovalType::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalTypes);
        $approvalType = $approvalTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalType = ApprovalType::factory()->create();

        $response = $this->get(route('approval-types.show', $approvalType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalTypeController::class,
            'update',
            \App\Http\Requests\ApprovalTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalType = ApprovalType::factory()->create();
        $name = $this->faker->name();
        $description = $this->faker->text();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-types.update', $approvalType), [
            'name' => $name,
            'description' => $description,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $approvalType->name);
        $this->assertEquals($description, $approvalType->description);
        $this->assertEquals($created_by->id, $approvalType->created_by);
        $this->assertEquals($modified_by->id, $approvalType->modified_by);
        $this->assertEquals($deleted_by->id, $approvalType->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalType = ApprovalType::factory()->create();

        $response = $this->delete(route('approval-types.destroy', $approvalType));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalType);
    }
}
