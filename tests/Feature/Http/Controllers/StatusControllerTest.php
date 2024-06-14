<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StatusController
 */
final class StatusControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $statuses = Status::factory()->count(3)->create();

        $response = $this->get(route('statuses.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StatusController::class,
            'store',
            \App\Http\Requests\StatusStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('statuses.store'), [
            'name' => $name,
        ]);

        $statuses = Status::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $statuses);
        $status = $statuses->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $status = Status::factory()->create();

        $response = $this->get(route('statuses.show', $status));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StatusController::class,
            'update',
            \App\Http\Requests\StatusUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $status = Status::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('statuses.update', $status), [
            'name' => $name,
        ]);

        $status->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $status->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $status = Status::factory()->create();

        $response = $this->delete(route('statuses.destroy', $status));

        $response->assertNoContent();

        $this->assertModelMissing($status);
    }
}
