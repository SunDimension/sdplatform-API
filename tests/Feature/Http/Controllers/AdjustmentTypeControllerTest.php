<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AdjustmentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AdjustmentTypeController
 */
final class AdjustmentTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $adjustmentTypes = AdjustmentType::factory()->count(3)->create();

        $response = $this->get(route('adjustment-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdjustmentTypeController::class,
            'store',
            \App\Http\Requests\AdjustmentTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('adjustment-types.store'), [
            'name' => $name,
        ]);

        $adjustmentTypes = AdjustmentType::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $adjustmentTypes);
        $adjustmentType = $adjustmentTypes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $adjustmentType = AdjustmentType::factory()->create();

        $response = $this->get(route('adjustment-types.show', $adjustmentType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdjustmentTypeController::class,
            'update',
            \App\Http\Requests\AdjustmentTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $adjustmentType = AdjustmentType::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('adjustment-types.update', $adjustmentType), [
            'name' => $name,
        ]);

        $adjustmentType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $adjustmentType->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $adjustmentType = AdjustmentType::factory()->create();

        $response = $this->delete(route('adjustment-types.destroy', $adjustmentType));

        $response->assertNoContent();

        $this->assertModelMissing($adjustmentType);
    }
}
