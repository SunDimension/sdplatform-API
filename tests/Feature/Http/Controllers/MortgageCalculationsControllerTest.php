<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\MortgageCalculation;
use App\Models\MortgageCalculations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MortgageCalculationsController
 */
final class MortgageCalculationsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $mortgageCalculations = MortgageCalculations::factory()->count(3)->create();

        $response = $this->get(route('mortgage-calculations.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MortgageCalculationsController::class,
            'store',
            \App\Http\Requests\MortgageCalculationsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $loan_amount = $this->faker->randomFloat(/** float_attributes **/);
        $interest_rate = $this->faker->randomFloat(/** float_attributes **/);
        $loan_term = $this->faker->numberBetween(-10000, 10000);
        $monthly_payment = $this->faker->randomFloat(/** float_attributes **/);
        $total_payment = $this->faker->randomFloat(/** float_attributes **/);

        $response = $this->post(route('mortgage-calculations.store'), [
            'loan_amount' => $loan_amount,
            'interest_rate' => $interest_rate,
            'loan_term' => $loan_term,
            'monthly_payment' => $monthly_payment,
            'total_payment' => $total_payment,
        ]);

        $mortgageCalculations = MortgageCalculation::query()
            ->where('loan_amount', $loan_amount)
            ->where('interest_rate', $interest_rate)
            ->where('loan_term', $loan_term)
            ->where('monthly_payment', $monthly_payment)
            ->where('total_payment', $total_payment)
            ->get();
        $this->assertCount(1, $mortgageCalculations);
        $mortgageCalculation = $mortgageCalculations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $mortgageCalculation = MortgageCalculations::factory()->create();

        $response = $this->get(route('mortgage-calculations.show', $mortgageCalculation));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MortgageCalculationsController::class,
            'update',
            \App\Http\Requests\MortgageCalculationsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $mortgageCalculation = MortgageCalculations::factory()->create();
        $loan_amount = $this->faker->randomFloat(/** float_attributes **/);
        $interest_rate = $this->faker->randomFloat(/** float_attributes **/);
        $loan_term = $this->faker->numberBetween(-10000, 10000);
        $monthly_payment = $this->faker->randomFloat(/** float_attributes **/);
        $total_payment = $this->faker->randomFloat(/** float_attributes **/);

        $response = $this->put(route('mortgage-calculations.update', $mortgageCalculation), [
            'loan_amount' => $loan_amount,
            'interest_rate' => $interest_rate,
            'loan_term' => $loan_term,
            'monthly_payment' => $monthly_payment,
            'total_payment' => $total_payment,
        ]);

        $mortgageCalculation->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($loan_amount, $mortgageCalculation->loan_amount);
        $this->assertEquals($interest_rate, $mortgageCalculation->interest_rate);
        $this->assertEquals($loan_term, $mortgageCalculation->loan_term);
        $this->assertEquals($monthly_payment, $mortgageCalculation->monthly_payment);
        $this->assertEquals($total_payment, $mortgageCalculation->total_payment);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $mortgageCalculation = MortgageCalculations::factory()->create();
        $mortgageCalculation = MortgageCalculation::factory()->create();

        $response = $this->delete(route('mortgage-calculations.destroy', $mortgageCalculation));

        $response->assertNoContent();

        $this->assertModelMissing($mortgageCalculation);
    }
}
