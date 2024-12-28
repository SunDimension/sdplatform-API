<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChangeReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChangeReasonController
 */
final class ChangeReasonControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $changeReasons = ChangeReason::factory()->count(3)->create();

        $response = $this->get(route('change-reasons.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChangeReasonController::class,
            'store',
            \App\Http\Requests\ChangeReasonStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $created_at = Carbon::parse($this->faker->dateTime());
        $updated_at = Carbon::parse($this->faker->dateTime());

        $response = $this->post(route('change-reasons.store'), [
            'name' => $name,
            'created_at' => $created_at->toDateTimeString(),
            'updated_at' => $updated_at->toDateTimeString(),
        ]);

        $changeReasons = ChangeReason::query()
            ->where('name', $name)
            ->where('created_at', $created_at)
            ->where('updated_at', $updated_at)
            ->get();
        $this->assertCount(1, $changeReasons);
        $changeReason = $changeReasons->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $changeReason = ChangeReason::factory()->create();

        $response = $this->get(route('change-reasons.show', $changeReason));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChangeReasonController::class,
            'update',
            \App\Http\Requests\ChangeReasonUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $changeReason = ChangeReason::factory()->create();
        $name = $this->faker->name();
        $created_at = Carbon::parse($this->faker->dateTime());
        $updated_at = Carbon::parse($this->faker->dateTime());

        $response = $this->put(route('change-reasons.update', $changeReason), [
            'name' => $name,
            'created_at' => $created_at->toDateTimeString(),
            'updated_at' => $updated_at->toDateTimeString(),
        ]);

        $changeReason->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $changeReason->name);
        $this->assertEquals($created_at->timestamp, $changeReason->created_at);
        $this->assertEquals($updated_at->timestamp, $changeReason->updated_at);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $changeReason = ChangeReason::factory()->create();

        $response = $this->delete(route('change-reasons.destroy', $changeReason));

        $response->assertNoContent();

        $this->assertModelMissing($changeReason);
    }
}
