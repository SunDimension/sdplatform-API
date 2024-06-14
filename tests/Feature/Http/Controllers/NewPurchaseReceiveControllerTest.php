<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\NewPurchaseReceive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NewPurchaseReceiveController
 */
final class NewPurchaseReceiveControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $newPurchaseReceives = NewPurchaseReceive::factory()->count(3)->create();

        $response = $this->get(route('new-purchase-receives.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseReceiveController::class,
            'store',
            \App\Http\Requests\NewPurchaseReceiveStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('new-purchase-receives.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(newPurchaseReceives, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $newPurchaseReceive = NewPurchaseReceive::factory()->create();

        $response = $this->get(route('new-purchase-receives.show', $newPurchaseReceive));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseReceiveController::class,
            'update',
            \App\Http\Requests\NewPurchaseReceiveUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $newPurchaseReceive = NewPurchaseReceive::factory()->create();

        $response = $this->put(route('new-purchase-receives.update', $newPurchaseReceive));

        $newPurchaseReceive->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $newPurchaseReceive = NewPurchaseReceive::factory()->create();

        $response = $this->delete(route('new-purchase-receives.destroy', $newPurchaseReceive));

        $response->assertNoContent();

        $this->assertModelMissing($newPurchaseReceive);
    }
}
