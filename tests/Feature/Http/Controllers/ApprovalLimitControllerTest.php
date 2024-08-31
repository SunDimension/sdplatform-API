<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalLimit;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ApprovalLimitController
 */
final class ApprovalLimitControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $approvalLimits = ApprovalLimit::factory()->count(3)->create();

        $response = $this->get(route('approval-limits.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalLimitController::class,
            'store',
            \App\Http\Requests\ApprovalLimitStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $amount = $this->faker->randomFloat(/** float_attributes **/);
        $user = User::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('approval-limits.store'), [
            'amount' => $amount,
            'user_id' => $user->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalLimits = ApprovalLimit::query()
            ->where('amount', $amount)
            ->where('user_id', $user->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $approvalLimits);
        $approvalLimit = $approvalLimits->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $approvalLimit = ApprovalLimit::factory()->create();

        $response = $this->get(route('approval-limits.show', $approvalLimit));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ApprovalLimitController::class,
            'update',
            \App\Http\Requests\ApprovalLimitUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $approvalLimit = ApprovalLimit::factory()->create();
        $amount = $this->faker->randomFloat(/** float_attributes **/);
        $user = User::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('approval-limits.update', $approvalLimit), [
            'amount' => $amount,
            'user_id' => $user->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $approvalLimit->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($amount, $approvalLimit->amount);
        $this->assertEquals($user->id, $approvalLimit->user_id);
        $this->assertEquals($created_by->id, $approvalLimit->created_by);
        $this->assertEquals($modified_by->id, $approvalLimit->modified_by);
        $this->assertEquals($deleted_by->id, $approvalLimit->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $approvalLimit = ApprovalLimit::factory()->create();

        $response = $this->delete(route('approval-limits.destroy', $approvalLimit));

        $response->assertNoContent();

        $this->assertSoftDeleted($approvalLimit);
    }
}
