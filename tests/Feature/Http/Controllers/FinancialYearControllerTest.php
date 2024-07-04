<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FinancialYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FinancialYearController
 */
final class FinancialYearControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $financialYears = FinancialYear::factory()->count(3)->create();

        $response = $this->get(route('financial-years.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialYearController::class,
            'store',
            \App\Http\Requests\FinancialYearStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $date_from = Carbon::parse($this->faker->date());
        $date_to = Carbon::parse($this->faker->date());
        $is_active = $this->faker->boolean();

        $response = $this->post(route('financial-years.store'), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
        ]);

        $financialYears = FinancialYear::query()
            ->where('name', $name)
            ->where('date_from', $date_from)
            ->where('date_to', $date_to)
            ->where('is_active', $is_active)
            ->get();
        $this->assertCount(1, $financialYears);
        $financialYear = $financialYears->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $financialYear = FinancialYear::factory()->create();

        $response = $this->get(route('financial-years.show', $financialYear));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialYearController::class,
            'update',
            \App\Http\Requests\FinancialYearUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $financialYear = FinancialYear::factory()->create();
        $name = $this->faker->name();
        $date_from = Carbon::parse($this->faker->date());
        $date_to = Carbon::parse($this->faker->date());
        $is_active = $this->faker->boolean();

        $response = $this->put(route('financial-years.update', $financialYear), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
        ]);

        $financialYear->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $financialYear->name);
        $this->assertEquals($date_from, $financialYear->date_from);
        $this->assertEquals($date_to, $financialYear->date_to);
        $this->assertEquals($is_active, $financialYear->is_active);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $financialYear = FinancialYear::factory()->create();

        $response = $this->delete(route('financial-years.destroy', $financialYear));

        $response->assertNoContent();

        $this->assertModelMissing($financialYear);
    }
}
