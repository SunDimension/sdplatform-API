<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\WarehouseController
 */
final class WarehouseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $warehouses = Warehouse::factory()->count(3)->create();

        $response = $this->get(route('warehouses.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WarehouseController::class,
            'store',
            \App\Http\Requests\WarehouseStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $branch = Branch::factory()->create();
        $warehouse_address = $this->faker->word();
        $zipcode = $this->faker->word();
        $contact_person = $this->faker->word();
        $email = $this->faker->safeEmail();
        $phone = $this->faker->phoneNumber();

        $response = $this->post(route('warehouses.store'), [
            'name' => $name,
            'branch_id' => $branch->id,
            'warehouse_address' => $warehouse_address,
            'zipcode' => $zipcode,
            'contact_person' => $contact_person,
            'email' => $email,
            'phone' => $phone,
        ]);

        $warehouses = Warehouse::query()
            ->where('name', $name)
            ->where('branch_id', $branch->id)
            ->where('warehouse_address', $warehouse_address)
            ->where('zipcode', $zipcode)
            ->where('contact_person', $contact_person)
            ->where('email', $email)
            ->where('phone', $phone)
            ->get();
        $this->assertCount(1, $warehouses);
        $warehouse = $warehouses->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->get(route('warehouses.show', $warehouse));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\WarehouseController::class,
            'update',
            \App\Http\Requests\WarehouseUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $warehouse = Warehouse::factory()->create();
        $name = $this->faker->name();
        $branch = Branch::factory()->create();
        $warehouse_address = $this->faker->word();
        $zipcode = $this->faker->word();
        $contact_person = $this->faker->word();
        $email = $this->faker->safeEmail();
        $phone = $this->faker->phoneNumber();

        $response = $this->put(route('warehouses.update', $warehouse), [
            'name' => $name,
            'branch_id' => $branch->id,
            'warehouse_address' => $warehouse_address,
            'zipcode' => $zipcode,
            'contact_person' => $contact_person,
            'email' => $email,
            'phone' => $phone,
        ]);

        $warehouse->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $warehouse->name);
        $this->assertEquals($branch->id, $warehouse->branch_id);
        $this->assertEquals($warehouse_address, $warehouse->warehouse_address);
        $this->assertEquals($zipcode, $warehouse->zipcode);
        $this->assertEquals($contact_person, $warehouse->contact_person);
        $this->assertEquals($email, $warehouse->email);
        $this->assertEquals($phone, $warehouse->phone);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->delete(route('warehouses.destroy', $warehouse));

        $response->assertNoContent();

        $this->assertModelMissing($warehouse);
    }
}
