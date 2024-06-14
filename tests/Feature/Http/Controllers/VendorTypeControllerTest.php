<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\VendorType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VendorTypeController
 */
final class VendorTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $vendorTypes = VendorType::factory()->count(3)->create();

        $response = $this->get(route('vendor-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorTypeController::class,
            'store',
            \App\Http\Requests\VendorTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('vendor-types.store'), [
            'name' => $name,
        ]);

        $vendorTypes = VendorType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $vendorTypes);
        $vendorType = $vendorTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $vendorType = VendorType::factory()->create();

        $response = $this->get(route('vendor-types.show', $vendorType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorTypeController::class,
            'update',
            \App\Http\Requests\VendorTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $vendorType = VendorType::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('vendor-types.update', $vendorType), [
            'name' => $name,
        ]);

        $vendorType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $vendorType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $vendorType = VendorType::factory()->create();

        $response = $this->delete(route('vendor-types.destroy', $vendorType));

        $response->assertNoContent();

        $this->assertModelMissing($vendorType);
    }
}
