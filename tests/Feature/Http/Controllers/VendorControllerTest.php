<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Bank;
use App\Models\Vendor;
use App\Models\VendorType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VendorController
 */
final class VendorControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $vendors = Vendor::factory()->count(3)->create();

        $response = $this->get(route('vendors.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorController::class,
            'store',
            \App\Http\Requests\VendorStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $address = $this->faker->word();
        $contact_title = $this->faker->word();
        $contact_designation = $this->faker->word();
        $contact_surname = $this->faker->word();
        $contact_firstname = $this->faker->word();
        $contact_middlename = $this->faker->word();
        $contact_fullname = $this->faker->word();
        $vendor_type = VendorType::factory()->create();
        $phone_number = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $image_url = $this->faker->word();
        $tin = $this->faker->word();
        $bank = Bank::factory()->create();
        $account_number = $this->faker->word();

        $response = $this->post(route('vendors.store'), [
            'name' => $name,
            'address' => $address,
            'contact_title' => $contact_title,
            'contact_designation' => $contact_designation,
            'contact_surname' => $contact_surname,
            'contact_firstname' => $contact_firstname,
            'contact_middlename' => $contact_middlename,
            'contact_fullname' => $contact_fullname,
            'vendor_type_id' => $vendor_type->id,
            'phone_number' => $phone_number,
            'email' => $email,
            'image_url' => $image_url,
            'tin' => $tin,
            'bank_id' => $bank->id,
            'account_number' => $account_number,
        ]);

        $vendors = Vendor::query()
            ->where('name', $name)
            ->where('address', $address)
            ->where('contact_title', $contact_title)
            ->where('contact_designation', $contact_designation)
            ->where('contact_surname', $contact_surname)
            ->where('contact_firstname', $contact_firstname)
            ->where('contact_middlename', $contact_middlename)
            ->where('contact_fullname', $contact_fullname)
            ->where('vendor_type_id', $vendor_type->id)
            ->where('phone_number', $phone_number)
            ->where('email', $email)
            ->where('image_url', $image_url)
            ->where('tin', $tin)
            ->where('bank_id', $bank->id)
            ->where('account_number', $account_number)
            ->get();
        $this->assertCount(1, $vendors);
        $vendor = $vendors->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get(route('vendors.show', $vendor));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorController::class,
            'update',
            \App\Http\Requests\VendorUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $vendor = Vendor::factory()->create();
        $name = $this->faker->name();
        $address = $this->faker->word();
        $contact_title = $this->faker->word();
        $contact_designation = $this->faker->word();
        $contact_surname = $this->faker->word();
        $contact_firstname = $this->faker->word();
        $contact_middlename = $this->faker->word();
        $contact_fullname = $this->faker->word();
        $vendor_type = VendorType::factory()->create();
        $phone_number = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $image_url = $this->faker->word();
        $tin = $this->faker->word();
        $bank = Bank::factory()->create();
        $account_number = $this->faker->word();

        $response = $this->put(route('vendors.update', $vendor), [
            'name' => $name,
            'address' => $address,
            'contact_title' => $contact_title,
            'contact_designation' => $contact_designation,
            'contact_surname' => $contact_surname,
            'contact_firstname' => $contact_firstname,
            'contact_middlename' => $contact_middlename,
            'contact_fullname' => $contact_fullname,
            'vendor_type_id' => $vendor_type->id,
            'phone_number' => $phone_number,
            'email' => $email,
            'image_url' => $image_url,
            'tin' => $tin,
            'bank_id' => $bank->id,
            'account_number' => $account_number,
        ]);

        $vendor->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $vendor->name);
        $this->assertEquals($address, $vendor->address);
        $this->assertEquals($contact_title, $vendor->contact_title);
        $this->assertEquals($contact_designation, $vendor->contact_designation);
        $this->assertEquals($contact_surname, $vendor->contact_surname);
        $this->assertEquals($contact_firstname, $vendor->contact_firstname);
        $this->assertEquals($contact_middlename, $vendor->contact_middlename);
        $this->assertEquals($contact_fullname, $vendor->contact_fullname);
        $this->assertEquals($vendor_type->id, $vendor->vendor_type_id);
        $this->assertEquals($phone_number, $vendor->phone_number);
        $this->assertEquals($email, $vendor->email);
        $this->assertEquals($image_url, $vendor->image_url);
        $this->assertEquals($tin, $vendor->tin);
        $this->assertEquals($bank->id, $vendor->bank_id);
        $this->assertEquals($account_number, $vendor->account_number);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->delete(route('vendors.destroy', $vendor));

        $response->assertNoContent();

        $this->assertModelMissing($vendor);
    }
}
