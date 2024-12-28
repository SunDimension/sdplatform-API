<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PriceChange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PriceChangeController
 */
final class PriceChangeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $priceChanges = PriceChange::factory()->count(3)->create();

        $response = $this->get(route('price-changes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PriceChangeController::class,
            'store',
            \App\Http\Requests\PriceChangeControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $details = $this->faker->;
        $store_id = $this->faker->word();
        $branch_id = $this->faker->word();
        $change_reason_id = $this->faker->word();
        $status = $this->faker->randomElement(/** enum_attributes **/);
        $comment = $this->faker->word();
        $created_by = $this->faker->word();
        $created_at = Carbon::parse($this->faker->dateTime());
        $updated_at = Carbon::parse($this->faker->dateTime());

        $response = $this->post(route('price-changes.store'), [
            'details' => $details,
            'store_id' => $store_id,
            'branch_id' => $branch_id,
            'change_reason_id' => $change_reason_id,
            'status' => $status,
            'comment' => $comment,
            'created_by' => $created_by,
            'created_at' => $created_at->toDateTimeString(),
            'updated_at' => $updated_at->toDateTimeString(),
        ]);

        $priceChanges = PriceChange::query()
            ->where('details', $details)
            ->where('store_id', $store_id)
            ->where('branch_id', $branch_id)
            ->where('change_reason_id', $change_reason_id)
            ->where('status', $status)
            ->where('comment', $comment)
            ->where('created_by', $created_by)
            ->where('created_at', $created_at)
            ->where('updated_at', $updated_at)
            ->get();
        $this->assertCount(1, $priceChanges);
        $priceChange = $priceChanges->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $priceChange = PriceChange::factory()->create();

        $response = $this->get(route('price-changes.show', $priceChange));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PriceChangeController::class,
            'update',
            \App\Http\Requests\PriceChangeControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $priceChange = PriceChange::factory()->create();
        $details = $this->faker->;
        $store_id = $this->faker->word();
        $branch_id = $this->faker->word();
        $change_reason_id = $this->faker->word();
        $status = $this->faker->randomElement(/** enum_attributes **/);
        $comment = $this->faker->word();
        $created_by = $this->faker->word();
        $created_at = Carbon::parse($this->faker->dateTime());
        $updated_at = Carbon::parse($this->faker->dateTime());

        $response = $this->put(route('price-changes.update', $priceChange), [
            'details' => $details,
            'store_id' => $store_id,
            'branch_id' => $branch_id,
            'change_reason_id' => $change_reason_id,
            'status' => $status,
            'comment' => $comment,
            'created_by' => $created_by,
            'created_at' => $created_at->toDateTimeString(),
            'updated_at' => $updated_at->toDateTimeString(),
        ]);

        $priceChange->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($details, $priceChange->details);
        $this->assertEquals($store_id, $priceChange->store_id);
        $this->assertEquals($branch_id, $priceChange->branch_id);
        $this->assertEquals($change_reason_id, $priceChange->change_reason_id);
        $this->assertEquals($status, $priceChange->status);
        $this->assertEquals($comment, $priceChange->comment);
        $this->assertEquals($created_by, $priceChange->created_by);
        $this->assertEquals($created_at->timestamp, $priceChange->created_at);
        $this->assertEquals($updated_at->timestamp, $priceChange->updated_at);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $priceChange = PriceChange::factory()->create();

        $response = $this->delete(route('price-changes.destroy', $priceChange));

        $response->assertNoContent();

        $this->assertModelMissing($priceChange);
    }
}
