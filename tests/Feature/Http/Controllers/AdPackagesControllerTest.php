<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AdPackage;
use App\Models\AdPackages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AdPackagesController
 */
final class AdPackagesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $adPackages = AdPackages::factory()->count(3)->create();

        $response = $this->get(route('ad-packages.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdPackagesController::class,
            'store',
            \App\Http\Requests\AdPackagesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $duration = $this->faker->numberBetween(-10000, 10000);
        $homepage = $this->faker->boolean();
        $category_page = $this->faker->boolean();
        $search_page = $this->faker->boolean();

        $response = $this->post(route('ad-packages.store'), [
            'name' => $name,
            'price' => $price,
            'duration' => $duration,
            'homepage' => $homepage,
            'category_page' => $category_page,
            'search_page' => $search_page,
        ]);

        $adPackages = AdPackage::query()
            ->where('name', $name)
            ->where('price', $price)
            ->where('duration', $duration)
            ->where('homepage', $homepage)
            ->where('category_page', $category_page)
            ->where('search_page', $search_page)
            ->get();
        $this->assertCount(1, $adPackages);
        $adPackage = $adPackages->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $adPackage = AdPackages::factory()->create();

        $response = $this->get(route('ad-packages.show', $adPackage));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdPackagesController::class,
            'update',
            \App\Http\Requests\AdPackagesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $adPackage = AdPackages::factory()->create();
        $name = $this->faker->name();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $duration = $this->faker->numberBetween(-10000, 10000);
        $homepage = $this->faker->boolean();
        $category_page = $this->faker->boolean();
        $search_page = $this->faker->boolean();

        $response = $this->put(route('ad-packages.update', $adPackage), [
            'name' => $name,
            'price' => $price,
            'duration' => $duration,
            'homepage' => $homepage,
            'category_page' => $category_page,
            'search_page' => $search_page,
        ]);

        $adPackage->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $adPackage->name);
        $this->assertEquals($price, $adPackage->price);
        $this->assertEquals($duration, $adPackage->duration);
        $this->assertEquals($homepage, $adPackage->homepage);
        $this->assertEquals($category_page, $adPackage->category_page);
        $this->assertEquals($search_page, $adPackage->search_page);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $adPackage = AdPackages::factory()->create();
        $adPackage = AdPackage::factory()->create();

        $response = $this->delete(route('ad-packages.destroy', $adPackage));

        $response->assertNoContent();

        $this->assertModelMissing($adPackage);
    }
}
