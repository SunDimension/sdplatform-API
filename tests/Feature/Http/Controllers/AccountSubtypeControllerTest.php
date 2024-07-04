<?php

namespace Tests\Feature\Http\Controllers;

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
 * @see \App\Http\Controllers\AccountSubtypeController
 */
final class AccountSubtypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $accountSubtypes = AccountSubtype::factory()->count(3)->create();

        $response = $this->get(route('account-subtypes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountSubtypeController::class,
            'store',
            \App\Http\Requests\AccountSubtypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $account_type = AccountType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('account-subtypes.store'), [
            'name' => $name,
            'account_type_id' => $account_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountSubtypes = AccountSubtype::query()
            ->where('name', $name)
            ->where('account_type_id', $account_type->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $accountSubtypes);
        $accountSubtype = $accountSubtypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $accountSubtype = AccountSubtype::factory()->create();

        $response = $this->get(route('account-subtypes.show', $accountSubtype));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AccountSubtypeController::class,
            'update',
            \App\Http\Requests\AccountSubtypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $accountSubtype = AccountSubtype::factory()->create();
        $name = $this->faker->name();
        $account_type = AccountType::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('account-subtypes.update', $accountSubtype), [
            'name' => $name,
            'account_type_id' => $account_type->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $accountSubtype->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $accountSubtype->name);
        $this->assertEquals($account_type->id, $accountSubtype->account_type_id);
        $this->assertEquals($created_by->id, $accountSubtype->created_by);
        $this->assertEquals($modified_by->id, $accountSubtype->modified_by);
        $this->assertEquals($deleted_by->id, $accountSubtype->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $accountSubtype = AccountSubtype::factory()->create();

        $response = $this->delete(route('account-subtypes.destroy', $accountSubtype));

        $response->assertNoContent();

        $this->assertModelMissing($accountSubtype);
    }
}
