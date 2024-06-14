<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BrandController
 */
final class BrandControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $brands = Brand::factory()->count(3)->create();

        $response = $this->get(route('brands.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BrandController::class,
            'store',
            \App\Http\Requests\BrandStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('brands.store'), [
            'name' => $name,
        ]);

        $brands = Brand::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $brands);
        $brand = $brands->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->get(route('brands.show', $brand));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BrandController::class,
            'update',
            \App\Http\Requests\BrandUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $brand = Brand::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('brands.update', $brand), [
            'name' => $name,
        ]);

        $brand->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $brand->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->delete(route('brands.destroy', $brand));

        $response->assertNoContent();

        $this->assertModelMissing($brand);
    }
}
