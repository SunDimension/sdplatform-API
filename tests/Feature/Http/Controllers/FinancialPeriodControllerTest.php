<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FinancialPeriod;
use App\Models\FinancialQuarter;
use App\Models\FinancialYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FinancialPeriodController
 */
final class FinancialPeriodControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $financialPeriods = FinancialPeriod::factory()->count(3)->create();

        $response = $this->get(route('financial-periods.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialPeriodController::class,
            'store',
            \App\Http\Requests\FinancialPeriodStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $date_from = Carbon::parse($this->faker->date());
        $date_to = Carbon::parse($this->faker->date());
        $is_active = $this->faker->boolean();
        $financial_year = FinancialYear::factory()->create();
        $financial_quarter = FinancialQuarter::factory()->create();

        $response = $this->post(route('financial-periods.store'), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
            'financial_year_id' => $financial_year->id,
            'financial_quarter_id' => $financial_quarter->id,
        ]);

        $financialPeriods = FinancialPeriod::query()
            ->where('name', $name)
            ->where('date_from', $date_from)
            ->where('date_to', $date_to)
            ->where('is_active', $is_active)
            ->where('financial_year_id', $financial_year->id)
            ->where('financial_quarter_id', $financial_quarter->id)
            ->get();
        $this->assertCount(1, $financialPeriods);
        $financialPeriod = $financialPeriods->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $financialPeriod = FinancialPeriod::factory()->create();

        $response = $this->get(route('financial-periods.show', $financialPeriod));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialPeriodController::class,
            'update',
            \App\Http\Requests\FinancialPeriodUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $financialPeriod = FinancialPeriod::factory()->create();
        $name = $this->faker->name();
        $date_from = Carbon::parse($this->faker->date());
        $date_to = Carbon::parse($this->faker->date());
        $is_active = $this->faker->boolean();
        $financial_year = FinancialYear::factory()->create();
        $financial_quarter = FinancialQuarter::factory()->create();

        $response = $this->put(route('financial-periods.update', $financialPeriod), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
            'financial_year_id' => $financial_year->id,
            'financial_quarter_id' => $financial_quarter->id,
        ]);

        $financialPeriod->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $financialPeriod->name);
        $this->assertEquals($date_from, $financialPeriod->date_from);
        $this->assertEquals($date_to, $financialPeriod->date_to);
        $this->assertEquals($is_active, $financialPeriod->is_active);
        $this->assertEquals($financial_year->id, $financialPeriod->financial_year_id);
        $this->assertEquals($financial_quarter->id, $financialPeriod->financial_quarter_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $financialPeriod = FinancialPeriod::factory()->create();

        $response = $this->delete(route('financial-periods.destroy', $financialPeriod));

        $response->assertNoContent();

        $this->assertModelMissing($financialPeriod);
    }
}
