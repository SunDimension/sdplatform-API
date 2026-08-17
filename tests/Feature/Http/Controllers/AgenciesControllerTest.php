<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Agencies;
use App\Models\Agency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AgenciesController
 */
final class AgenciesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $agencies = Agencies::factory()->count(3)->create();

        $response = $this->get(route('agencies.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AgenciesController::class,
            'store',
            \App\Http\Requests\AgenciesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $company_name = $this->faker->word();
        $registeration_number = $this->faker->word();
        $address = $this->faker->word();
        $phone = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $status = $this->faker->word();

        $response = $this->post(route('agencies.store'), [
            'company_name' => $company_name,
            'registeration_number' => $registeration_number,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
        ]);

        $agencies = Agency::query()
            ->where('company_name', $company_name)
            ->where('registeration_number', $registeration_number)
            ->where('address', $address)
            ->where('phone', $phone)
            ->where('email', $email)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $agencies);
        $agency = $agencies->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $agency = Agencies::factory()->create();

        $response = $this->get(route('agencies.show', $agency));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AgenciesController::class,
            'update',
            \App\Http\Requests\AgenciesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $agency = Agencies::factory()->create();
        $company_name = $this->faker->word();
        $registeration_number = $this->faker->word();
        $address = $this->faker->word();
        $phone = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $status = $this->faker->word();

        $response = $this->put(route('agencies.update', $agency), [
            'company_name' => $company_name,
            'registeration_number' => $registeration_number,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
        ]);

        $agency->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($company_name, $agency->company_name);
        $this->assertEquals($registeration_number, $agency->registeration_number);
        $this->assertEquals($address, $agency->address);
        $this->assertEquals($phone, $agency->phone);
        $this->assertEquals($email, $agency->email);
        $this->assertEquals($status, $agency->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $agency = Agencies::factory()->create();
        $agency = Agency::factory()->create();

        $response = $this->delete(route('agencies.destroy', $agency));

        $response->assertNoContent();

        $this->assertModelMissing($agency);
    }
}
