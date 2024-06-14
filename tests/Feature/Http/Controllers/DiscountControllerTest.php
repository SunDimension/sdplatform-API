<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DiscountController
 */
final class DiscountControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $discounts = Discount::factory()->count(3)->create();

        $response = $this->get(route('discounts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DiscountController::class,
            'store',
            \App\Http\Requests\DiscountStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('discounts.store'), [
            'name' => $name,
        ]);

        $discounts = Discount::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $discounts);
        $discount = $discounts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $discount = Discount::factory()->create();

        $response = $this->get(route('discounts.show', $discount));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DiscountController::class,
            'update',
            \App\Http\Requests\DiscountUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $discount = Discount::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('discounts.update', $discount), [
            'name' => $name,
        ]);

        $discount->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $discount->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $discount = Discount::factory()->create();

        $response = $this->delete(route('discounts.destroy', $discount));

        $response->assertNoContent();

        $this->assertModelMissing($discount);
    }
}
