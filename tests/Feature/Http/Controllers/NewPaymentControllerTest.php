<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\NewPayment;
use App\Models\PaymentMode;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NewPaymentController
 */
final class NewPaymentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $newPayments = NewPayment::factory()->count(3)->create();

        $response = $this->get(route('new-payments.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPaymentController::class,
            'store',
            \App\Http\Requests\NewPaymentStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $vendor = Vendor::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $payment_amount = $this->faker->word();
        $payment_mode = PaymentMode::factory()->create();
        $description = $this->faker->text();

        $response = $this->post(route('new-payments.store'), [
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'payment_amount' => $payment_amount,
            'payment_mode_id' => $payment_mode->id,
            'description' => $description,
        ]);

        $newPayments = NewPayment::query()
            ->where('vendor_id', $vendor->id)
            ->where('branch_id', $branch->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('payment_amount', $payment_amount)
            ->where('payment_mode_id', $payment_mode->id)
            ->where('description', $description)
            ->get();
        $this->assertCount(1, $newPayments);
        $newPayment = $newPayments->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $newPayment = NewPayment::factory()->create();

        $response = $this->get(route('new-payments.show', $newPayment));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPaymentController::class,
            'update',
            \App\Http\Requests\NewPaymentUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $newPayment = NewPayment::factory()->create();
        $vendor = Vendor::factory()->create();
        $branch = Branch::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $payment_amount = $this->faker->word();
        $payment_mode = PaymentMode::factory()->create();
        $description = $this->faker->text();

        $response = $this->put(route('new-payments.update', $newPayment), [
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'payment_amount' => $payment_amount,
            'payment_mode_id' => $payment_mode->id,
            'description' => $description,
        ]);

        $newPayment->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($vendor->id, $newPayment->vendor_id);
        $this->assertEquals($branch->id, $newPayment->branch_id);
        $this->assertEquals($warehouse->id, $newPayment->warehouse_id);
        $this->assertEquals($payment_amount, $newPayment->payment_amount);
        $this->assertEquals($payment_mode->id, $newPayment->payment_mode_id);
        $this->assertEquals($description, $newPayment->description);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $newPayment = NewPayment::factory()->create();

        $response = $this->delete(route('new-payments.destroy', $newPayment));

        $response->assertNoContent();

        $this->assertModelMissing($newPayment);
    }
}
