<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Designation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DesignationController
 */
final class DesignationControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $designations = Designation::factory()->count(3)->create();

        $response = $this->get(route('designations.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DesignationController::class,
            'store',
            \App\Http\Requests\DesignationStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('designations.store'), [
            'name' => $name,
        ]);

        $designations = Designation::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $designations);
        $designation = $designations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $designation = Designation::factory()->create();

        $response = $this->get(route('designations.show', $designation));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DesignationController::class,
            'update',
            \App\Http\Requests\DesignationUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $designation = Designation::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('designations.update', $designation), [
            'name' => $name,
        ]);

        $designation->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $designation->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $designation = Designation::factory()->create();

        $response = $this->delete(route('designations.destroy', $designation));

        $response->assertNoContent();

        $this->assertModelMissing($designation);
    }
}
