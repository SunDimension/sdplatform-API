<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Bank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BankController
 */
final class BankControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $banks = Bank::factory()->count(3)->create();

        $response = $this->get(route('banks.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BankController::class,
            'store',
            \App\Http\Requests\BankStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('banks.store'), [
            'name' => $name,
        ]);

        $banks = Bank::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $banks);
        $bank = $banks->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $bank = Bank::factory()->create();

        $response = $this->get(route('banks.show', $bank));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BankController::class,
            'update',
            \App\Http\Requests\BankUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $bank = Bank::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('banks.update', $bank), [
            'name' => $name,
        ]);

        $bank->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $bank->name);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $bank = Bank::factory()->create();

        $response = $this->delete(route('banks.destroy', $bank));

        $response->assertNoContent();

        $this->assertModelMissing($bank);
    }
}
