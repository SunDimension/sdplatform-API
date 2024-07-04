<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountSubtype;
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
 * @see \App\Http\Controllers\AccountController
 */
final class AccountControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $accounts = Account::factory()->count(3)->create();

        $response = $this->get(route('accounts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountController::class,
            'store',
            \App\Http\Requests\AccountStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $code = $this->faker->word();
        $account_group = AccountGroup::factory()->create();
        $account_type = AccountType::factory()->create();
        $account_subtype = AccountSubtype::factory()->create();
        $account_owner_id = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('accounts.store'), [
            'name' => $name,
            'code' => $code,
            'account_group_id' => $account_group->id,
            'account_type_id' => $account_type->id,
            'account_subtype_id' => $account_subtype->id,
            'account_owner_id' => $account_owner_id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accounts = Account::query()
            ->where('name', $name)
            ->where('code', $code)
            ->where('account_group_id', $account_group->id)
            ->where('account_type_id', $account_type->id)
            ->where('account_subtype_id', $account_subtype->id)
            ->where('account_owner_id', $account_owner_id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $accounts);
        $account = $accounts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $account = Account::factory()->create();

        $response = $this->get(route('accounts.show', $account));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountController::class,
            'update',
            \App\Http\Requests\AccountUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $account = Account::factory()->create();
        $name = $this->faker->name();
        $code = $this->faker->word();
        $account_group = AccountGroup::factory()->create();
        $account_type = AccountType::factory()->create();
        $account_subtype = AccountSubtype::factory()->create();
        $account_owner_id = $this->faker->word();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('accounts.update', $account), [
            'name' => $name,
            'code' => $code,
            'account_group_id' => $account_group->id,
            'account_type_id' => $account_type->id,
            'account_subtype_id' => $account_subtype->id,
            'account_owner_id' => $account_owner_id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $account->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $account->name);
        $this->assertEquals($code, $account->code);
        $this->assertEquals($account_group->id, $account->account_group_id);
        $this->assertEquals($account_type->id, $account->account_type_id);
        $this->assertEquals($account_subtype->id, $account->account_subtype_id);
        $this->assertEquals($account_owner_id, $account->account_owner_id);
        $this->assertEquals($created_by->id, $account->created_by);
        $this->assertEquals($modified_by->id, $account->modified_by);
        $this->assertEquals($deleted_by->id, $account->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $account = Account::factory()->create();

        $response = $this->delete(route('accounts.destroy', $account));

        $response->assertNoContent();

        $this->assertModelMissing($account);
    }
}
