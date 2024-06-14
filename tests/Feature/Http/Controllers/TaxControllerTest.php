<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TaxController
 */
final class TaxControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $taxes = Tax::factory()->count(3)->create();

        $response = $this->get(route('taxes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TaxController::class,
            'store',
            \App\Http\Requests\TaxStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('taxes.store'), [
            'name' => $name,
        ]);

        $taxes = Tax::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $taxes);
        $tax = $taxes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $tax = Tax::factory()->create();

        $response = $this->get(route('taxes.show', $tax));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TaxController::class,
            'update',
            \App\Http\Requests\TaxUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $tax = Tax::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('taxes.update', $tax), [
            'name' => $name,
        ]);

        $tax->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $tax->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $tax = Tax::factory()->create();

        $response = $this->delete(route('taxes.destroy', $tax));

        $response->assertNoContent();

        $this->assertModelMissing($tax);
    }
}
