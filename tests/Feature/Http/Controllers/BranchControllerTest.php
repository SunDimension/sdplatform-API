<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BranchController
 */
final class BranchControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $branches = Branch::factory()->count(3)->create();

        $response = $this->get(route('branches.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BranchController::class,
            'store',
            \App\Http\Requests\BranchStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $address = $this->faker->word();
        $contact_person = $this->faker->word();
        $email = $this->faker->safeEmail();
        $phone = $this->faker->phoneNumber();
        $state = State::factory()->create();
        $country = Country::factory()->create();

        $response = $this->post(route('branches.store'), [
            'name' => $name,
            'address' => $address,
            'contact_person' => $contact_person,
            'email' => $email,
            'phone' => $phone,
            'state_id' => $state->id,
            'country_id' => $country->id,
        ]);

        $branches = Branch::query()
            ->where('name', $name)
            ->where('address', $address)
            ->where('contact_person', $contact_person)
            ->where('email', $email)
            ->where('phone', $phone)
            ->where('state_id', $state->id)
            ->where('country_id', $country->id)
            ->get();
        $this->assertCount(1, $branches);
        $branch = $branches->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->get(route('branches.show', $branch));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BranchController::class,
            'update',
            \App\Http\Requests\BranchUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $branch = Branch::factory()->create();
        $name = $this->faker->name();
        $address = $this->faker->word();
        $contact_person = $this->faker->word();
        $email = $this->faker->safeEmail();
        $phone = $this->faker->phoneNumber();
        $state = State::factory()->create();
        $country = Country::factory()->create();

        $response = $this->put(route('branches.update', $branch), [
            'name' => $name,
            'address' => $address,
            'contact_person' => $contact_person,
            'email' => $email,
            'phone' => $phone,
            'state_id' => $state->id,
            'country_id' => $country->id,
        ]);

        $branch->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $branch->name);
        $this->assertEquals($address, $branch->address);
        $this->assertEquals($contact_person, $branch->contact_person);
        $this->assertEquals($email, $branch->email);
        $this->assertEquals($phone, $branch->phone);
        $this->assertEquals($state->id, $branch->state_id);
        $this->assertEquals($country->id, $branch->country_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->delete(route('branches.destroy', $branch));

        $response->assertNoContent();

        $this->assertModelMissing($branch);
    }
}
