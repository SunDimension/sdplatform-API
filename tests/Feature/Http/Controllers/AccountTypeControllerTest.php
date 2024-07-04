<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\AccountType;
use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AccountTypeController
 */
final class AccountTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $accountTypes = AccountType::factory()->count(3)->create();

        $response = $this->get(route('account-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountTypeController::class,
            'store',
            \App\Http\Requests\AccountTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $account_group = AccountGroup::factory()->create();
        $name = $this->faker->name();
        $code = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('account-types.store'), [
            'account_group_id' => $account_group->id,
            'name' => $name,
            'code' => $code,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountTypes = AccountType::query()
            ->where('account_group_id', $account_group->id)
            ->where('name', $name)
            ->where('code', $code)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $accountTypes);
        $accountType = $accountTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $accountType = AccountType::factory()->create();

        $response = $this->get(route('account-types.show', $accountType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountTypeController::class,
            'update',
            \App\Http\Requests\AccountTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $accountType = AccountType::factory()->create();
        $account_group = AccountGroup::factory()->create();
        $name = $this->faker->name();
        $code = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('account-types.update', $accountType), [
            'account_group_id' => $account_group->id,
            'name' => $name,
            'code' => $code,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($account_group->id, $accountType->account_group_id);
        $this->assertEquals($name, $accountType->name);
        $this->assertEquals($code, $accountType->code);
        $this->assertEquals($created_by->id, $accountType->created_by);
        $this->assertEquals($modified_by->id, $accountType->modified_by);
        $this->assertEquals($deleted_by->id, $accountType->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $accountType = AccountType::factory()->create();

        $response = $this->delete(route('account-types.destroy', $accountType));

        $response->assertNoContent();

        $this->assertModelMissing($accountType);
    }
}
