<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CustomerController
 */
final class CustomerControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $customers = Customer::factory()->count(3)->create();

        $response = $this->get(route('customers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CustomerController::class,
            'store',
            \App\Http\Requests\CustomerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $customer_type = CustomerType::factory()->create();
        $title = Title::factory()->create();
        $surname = $this->faker->word();
        $firstname = $this->faker->firstName();
        $middlename = $this->faker->word();
        $phone_number = $this->faker->phoneNumber();
        $fullname = $this->faker->word();

        $response = $this->post(route('customers.store'), [
            'customer_type_id' => $customer_type->id,
            'title_id' => $title->id,
            'surname' => $surname,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'phone_number' => $phone_number,
            'fullname' => $fullname,
        ]);

        $customers = Customer::query()
            ->where('customer_type_id', $customer_type->id)
            ->where('title_id', $title->id)
            ->where('surname', $surname)
            ->where('firstname', $firstname)
            ->where('middlename', $middlename)
            ->where('phone_number', $phone_number)
            ->where('fullname', $fullname)
            ->get();
        $this->assertCount(1, $customers);
        $customer = $customers->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->get(route('customers.show', $customer));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CustomerController::class,
            'update',
            \App\Http\Requests\CustomerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $customer = Customer::factory()->create();
        $customer_type = CustomerType::factory()->create();
        $title = Title::factory()->create();
        $surname = $this->faker->word();
        $firstname = $this->faker->firstName();
        $middlename = $this->faker->word();
        $phone_number = $this->faker->phoneNumber();
        $fullname = $this->faker->word();

        $response = $this->put(route('customers.update', $customer), [
            'customer_type_id' => $customer_type->id,
            'title_id' => $title->id,
            'surname' => $surname,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'phone_number' => $phone_number,
            'fullname' => $fullname,
        ]);

        $customer->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($customer_type->id, $customer->customer_type_id);
        $this->assertEquals($title->id, $customer->title_id);
        $this->assertEquals($surname, $customer->surname);
        $this->assertEquals($firstname, $customer->firstname);
        $this->assertEquals($middlename, $customer->middlename);
        $this->assertEquals($phone_number, $customer->phone_number);
        $this->assertEquals($fullname, $customer->fullname);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->delete(route('customers.destroy', $customer));

        $response->assertNoContent();

        $this->assertModelMissing($customer);
    }
}
