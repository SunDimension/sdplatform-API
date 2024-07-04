<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\DeletedBy;
use App\Models\FinancialQuarter;
use App\Models\FinancialYear;
use App\Models\ModifiedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FinancialQuarterController
 */
final class FinancialQuarterControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $financialQuarters = FinancialQuarter::factory()->count(3)->create();

        $response = $this->get(route('financial-quarters.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialQuarterController::class,
            'store',
            \App\Http\Requests\FinancialQuarterStoreRequest::class
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
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->post(route('financial-quarters.store'), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
            'financial_year_id' => $financial_year->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $financialQuarters = FinancialQuarter::query()
            ->where('name', $name)
            ->where('date_from', $date_from)
            ->where('date_to', $date_to)
            ->where('is_active', $is_active)
            ->where('financial_year_id', $financial_year->id)
            ->where('created_by', $created_by->id)
            ->where('modified_by', $modified_by->id)
            ->where('deleted_by', $deleted_by->id)
            ->get();
        $this->assertCount(1, $financialQuarters);
        $financialQuarter = $financialQuarters->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $financialQuarter = FinancialQuarter::factory()->create();

        $response = $this->get(route('financial-quarters.show', $financialQuarter));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FinancialQuarterController::class,
            'update',
            \App\Http\Requests\FinancialQuarterUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $financialQuarter = FinancialQuarter::factory()->create();
        $name = $this->faker->name();
        $date_from = Carbon::parse($this->faker->date());
        $date_to = Carbon::parse($this->faker->date());
        $is_active = $this->faker->boolean();
        $financial_year = FinancialYear::factory()->create();
        $created_by = CreatedBy::factory()->create();
        $modified_by = ModifiedBy::factory()->create();
        $deleted_by = DeletedBy::factory()->create();

        $response = $this->put(route('financial-quarters.update', $financialQuarter), [
            'name' => $name,
            'date_from' => $date_from->toDateString(),
            'date_to' => $date_to->toDateString(),
            'is_active' => $is_active,
            'financial_year_id' => $financial_year->id,
            'created_by' => $created_by->id,
            'modified_by' => $modified_by->id,
            'deleted_by' => $deleted_by->id,
        ]);

        $financialQuarter->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $financialQuarter->name);
        $this->assertEquals($date_from, $financialQuarter->date_from);
        $this->assertEquals($date_to, $financialQuarter->date_to);
        $this->assertEquals($is_active, $financialQuarter->is_active);
        $this->assertEquals($financial_year->id, $financialQuarter->financial_year_id);
        $this->assertEquals($created_by->id, $financialQuarter->created_by);
        $this->assertEquals($modified_by->id, $financialQuarter->modified_by);
        $this->assertEquals($deleted_by->id, $financialQuarter->deleted_by);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $financialQuarter = FinancialQuarter::factory()->create();

        $response = $this->delete(route('financial-quarters.destroy', $financialQuarter));

        $response->assertNoContent();

        $this->assertModelMissing($financialQuarter);
    }
}
