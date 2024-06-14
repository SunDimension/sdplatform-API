<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Models\Users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UsersController
 */
final class UsersControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $users = Users::factory()->count(3)->create();

        $response = $this->get(route('users.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UsersController::class,
            'store',
            \App\Http\Requests\UsersStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $role = Role::factory()->create();
        $user_name = $this->faker->userName();
        $user_email = $this->faker->word();
        $password = $this->faker->password();
        $status = Status::factory()->create();

        $response = $this->post(route('users.store'), [
            'role_id' => $role->id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'password' => $password,
            'status_id' => $status->id,
        ]);

        $users = User::query()
            ->where('role_id', $role->id)
            ->where('user_name', $user_name)
            ->where('user_email', $user_email)
            ->where('password', $password)
            ->where('status_id', $status->id)
            ->get();
        $this->assertCount(1, $users);
        $user = $users->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $user = Users::factory()->create();

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UsersController::class,
            'update',
            \App\Http\Requests\UsersUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $user = Users::factory()->create();
        $role = Role::factory()->create();
        $user_name = $this->faker->userName();
        $user_email = $this->faker->word();
        $password = $this->faker->password();
        $status = Status::factory()->create();

        $response = $this->put(route('users.update', $user), [
            'role_id' => $role->id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'password' => $password,
            'status_id' => $status->id,
        ]);

        $user->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals($user_name, $user->user_name);
        $this->assertEquals($user_email, $user->user_email);
        $this->assertEquals($password, $user->password);
        $this->assertEquals($status->id, $user->status_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $user = Users::factory()->create();
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertNoContent();

        $this->assertModelMissing($user);
    }
}
