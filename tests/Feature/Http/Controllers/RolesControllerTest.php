<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RolesController
 */
final class RolesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $roles = Roles::factory()->count(3)->create();

        $response = $this->get(route('roles.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolesController::class,
            'store',
            \App\Http\Requests\RolesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();

        $response = $this->post(route('roles.store'), [
            'name' => $name,
            'description' => $description,
        ]);

        $roles = Role::query()
            ->where('name', $name)
            ->where('description', $description)
            ->get();
        $this->assertCount(1, $roles);
        $role = $roles->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $role = Roles::factory()->create();

        $response = $this->get(route('roles.show', $role));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolesController::class,
            'update',
            \App\Http\Requests\RolesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $role = Roles::factory()->create();
        $name = $this->faker->name();
        $description = $this->faker->text();

        $response = $this->put(route('roles.update', $role), [
            'name' => $name,
            'description' => $description,
        ]);

        $role->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $role->name);
        $this->assertEquals($description, $role->description);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $role = Roles::factory()->create();
        $role = Role::factory()->create();

        $response = $this->delete(route('roles.destroy', $role));

        $response->assertNoContent();

        $this->assertModelMissing($role);
    }
}
