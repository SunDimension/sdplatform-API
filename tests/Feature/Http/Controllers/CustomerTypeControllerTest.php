<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CustomerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CustomerTypeController
 */
final class CustomerTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $customerTypes = CustomerType::factory()->count(3)->create();

        $response = $this->get(route('customer-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CustomerTypeController::class,
            'store',
            \App\Http\Requests\CustomerTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('customer-types.store'), [
            'name' => $name,
        ]);

        $customerTypes = CustomerType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $customerTypes);
        $customerType = $customerTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $customerType = CustomerType::factory()->create();

        $response = $this->get(route('customer-types.show', $customerType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CustomerTypeController::class,
            'update',
            \App\Http\Requests\CustomerTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $customerType = CustomerType::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('customer-types.update', $customerType), [
            'name' => $name,
        ]);

        $customerType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $customerType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $customerType = CustomerType::factory()->create();

        $response = $this->delete(route('customer-types.destroy', $customerType));

        $response->assertNoContent();

        $this->assertModelMissing($customerType);
    }
}
