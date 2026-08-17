<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
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
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $password = $this->faker->password();
        $email = $this->faker->safeEmail();
        $status = $this->faker->word();
        $email_verified = $this->faker->boolean();
        $phone_verified = $this->faker->boolean();
        $kyc_verified = $this->faker->boolean();

        $response = $this->post(route('users.store'), [
            'role_id' => $role->id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'password' => $password,
            'email' => $email,
            'status' => $status,
            'email_verified' => $email_verified,
            'phone_verified' => $phone_verified,
            'kyc_verified' => $kyc_verified,
        ]);

        $users = User::query()
            ->where('role_id', $role->id)
            ->where('firstname', $firstname)
            ->where('lastname', $lastname)
            ->where('password', $password)
            ->where('email', $email)
            ->where('status', $status)
            ->where('email_verified', $email_verified)
            ->where('phone_verified', $phone_verified)
            ->where('kyc_verified', $kyc_verified)
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
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $password = $this->faker->password();
        $email = $this->faker->safeEmail();
        $status = $this->faker->word();
        $email_verified = $this->faker->boolean();
        $phone_verified = $this->faker->boolean();
        $kyc_verified = $this->faker->boolean();

        $response = $this->put(route('users.update', $user), [
            'role_id' => $role->id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'password' => $password,
            'email' => $email,
            'status' => $status,
            'email_verified' => $email_verified,
            'phone_verified' => $phone_verified,
            'kyc_verified' => $kyc_verified,
        ]);

        $user->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals($firstname, $user->firstname);
        $this->assertEquals($lastname, $user->lastname);
        $this->assertEquals($password, $user->password);
        $this->assertEquals($email, $user->email);
        $this->assertEquals($status, $user->status);
        $this->assertEquals($email_verified, $user->email_verified);
        $this->assertEquals($phone_verified, $user->phone_verified);
        $this->assertEquals($kyc_verified, $user->kyc_verified);
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
