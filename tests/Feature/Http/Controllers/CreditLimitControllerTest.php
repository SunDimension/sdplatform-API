<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreditLimit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditLimitController
 */
final class CreditLimitControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $creditLimits = CreditLimit::factory()->count(3)->create();

        $response = $this->get(route('credit-limits.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditLimitController::class,
            'store',
            \App\Http\Requests\CreditLimitStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('credit-limits.store'), [
            'name' => $name,
        ]);

        $creditLimits = CreditLimit::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $creditLimits);
        $creditLimit = $creditLimits->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $creditLimit = CreditLimit::factory()->create();

        $response = $this->get(route('credit-limits.show', $creditLimit));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditLimitController::class,
            'update',
            \App\Http\Requests\CreditLimitUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $creditLimit = CreditLimit::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('credit-limits.update', $creditLimit), [
            'name' => $name,
        ]);

        $creditLimit->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $creditLimit->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $creditLimit = CreditLimit::factory()->create();

        $response = $this->delete(route('credit-limits.destroy', $creditLimit));

        $response->assertNoContent();

        $this->assertModelMissing($creditLimit);
    }
}
