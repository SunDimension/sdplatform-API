<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Countries;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CountriesController
 */
final class CountriesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $countries = Countries::factory()->count(3)->create();

        $response = $this->get(route('countries.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CountriesController::class,
            'store',
            \App\Http\Requests\CountriesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $code = $this->faker->word();

        $response = $this->post(route('countries.store'), [
            'name' => $name,
            'code' => $code,
        ]);

        $countries = Country::query()
            ->where('name', $name)
            ->where('code', $code)
            ->get();
        $this->assertCount(1, $countries);
        $country = $countries->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $country = Countries::factory()->create();

        $response = $this->get(route('countries.show', $country));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CountriesController::class,
            'update',
            \App\Http\Requests\CountriesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $country = Countries::factory()->create();
        $name = $this->faker->name();
        $code = $this->faker->word();

        $response = $this->put(route('countries.update', $country), [
            'name' => $name,
            'code' => $code,
        ]);

        $country->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $country->name);
        $this->assertEquals($code, $country->code);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $country = Countries::factory()->create();
        $country = Country::factory()->create();

        $response = $this->delete(route('countries.destroy', $country));

        $response->assertNoContent();

        $this->assertModelMissing($country);
    }
}
