<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Permission;
use App\Models\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PermissionsController
 */
final class PermissionsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $permissions = Permissions::factory()->count(3)->create();

        $response = $this->get(route('permissions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermissionsController::class,
            'store',
            \App\Http\Requests\PermissionsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('permissions.store'), [
            'name' => $name,
        ]);

        $permissions = Permission::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $permissions);
        $permission = $permissions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $permission = Permissions::factory()->create();

        $response = $this->get(route('permissions.show', $permission));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermissionsController::class,
            'update',
            \App\Http\Requests\PermissionsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $permission = Permissions::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('permissions.update', $permission), [
            'name' => $name,
        ]);

        $permission->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $permission->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $permission = Permissions::factory()->create();
        $permission = Permission::factory()->create();

        $response = $this->delete(route('permissions.destroy', $permission));

        $response->assertNoContent();

        $this->assertModelMissing($permission);
    }
}
