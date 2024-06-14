<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CountryController
 */
final class CountryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $countries = Country::factory()->count(3)->create();

        $response = $this->get(route('countries.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CountryController::class,
            'store',
            \App\Http\Requests\CountryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('countries.store'), [
            'name' => $name,
        ]);

        $countries = Country::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $countries);
        $country = $countries->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $country = Country::factory()->create();

        $response = $this->get(route('countries.show', $country));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CountryController::class,
            'update',
            \App\Http\Requests\CountryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $country = Country::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('countries.update', $country), [
            'name' => $name,
        ]);

        $country->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $country->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $country = Country::factory()->create();

        $response = $this->delete(route('countries.destroy', $country));

        $response->assertNoContent();

        $this->assertModelMissing($country);
    }
}
