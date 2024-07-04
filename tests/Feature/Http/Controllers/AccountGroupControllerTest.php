<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AccountGroupController
 */
final class AccountGroupControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $accountGroups = AccountGroup::factory()->count(3)->create();

        $response = $this->get(route('account-groups.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountGroupController::class,
            'store',
            \App\Http\Requests\AccountGroupStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('account-groups.store'), [
            'name' => $name,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountGroups = AccountGroup::query()
            ->where('name', $name)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $accountGroups);
        $accountGroup = $accountGroups->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $accountGroup = AccountGroup::factory()->create();

        $response = $this->get(route('account-groups.show', $accountGroup));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountGroupController::class,
            'update',
            \App\Http\Requests\AccountGroupUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $accountGroup = AccountGroup::factory()->create();
        $name = $this->faker->name();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('account-groups.update', $accountGroup), [
            'name' => $name,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountGroup->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $accountGroup->name);
        $this->assertEquals($created_by->id, $accountGroup->created_by);
        $this->assertEquals($modified_by->id, $accountGroup->modified_by);
        $this->assertEquals($deleted_by->id, $accountGroup->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $accountGroup = AccountGroup::factory()->create();

        $response = $this->delete(route('account-groups.destroy', $accountGroup));

        $response->assertNoContent();

        $this->assertModelMissing($accountGroup);
    }
}
