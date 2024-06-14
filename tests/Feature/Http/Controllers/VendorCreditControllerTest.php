<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorCredit;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VendorCreditController
 */
final class VendorCreditControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $vendorCredits = VendorCredit::factory()->count(3)->create();

        $response = $this->get(route('vendor-credits.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorCreditController::class,
            'store',
            \App\Http\Requests\VendorCreditStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $vendor = Vendor::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $credit_number = $this->faker->word();
        $purchase_order_number = $this->faker->word();
        $vendor_credit_date = Carbon::parse($this->faker->date());

        $response = $this->post(route('vendor-credits.store'), [
            'vendor_id' => $vendor->id,
            'warehouse_id' => $warehouse->id,
            'credit_number' => $credit_number,
            'purchase_order_number' => $purchase_order_number,
            'vendor_credit_date' => $vendor_credit_date->toDateString(),
        ]);

        $vendorCredits = VendorCredit::query()
            ->where('vendor_id', $vendor->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('credit_number', $credit_number)
            ->where('purchase_order_number', $purchase_order_number)
            ->where('vendor_credit_date', $vendor_credit_date)
            ->get();
        $this->assertCount(1, $vendorCredits);
        $vendorCredit = $vendorCredits->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $vendorCredit = VendorCredit::factory()->create();

        $response = $this->get(route('vendor-credits.show', $vendorCredit));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VendorCreditController::class,
            'update',
            \App\Http\Requests\VendorCreditUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $vendorCredit = VendorCredit::factory()->create();
        $vendor = Vendor::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $credit_number = $this->faker->word();
        $purchase_order_number = $this->faker->word();
        $vendor_credit_date = Carbon::parse($this->faker->date());

        $response = $this->put(route('vendor-credits.update', $vendorCredit), [
            'vendor_id' => $vendor->id,
            'warehouse_id' => $warehouse->id,
            'credit_number' => $credit_number,
            'purchase_order_number' => $purchase_order_number,
            'vendor_credit_date' => $vendor_credit_date->toDateString(),
        ]);

        $vendorCredit->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($vendor->id, $vendorCredit->vendor_id);
        $this->assertEquals($warehouse->id, $vendorCredit->warehouse_id);
        $this->assertEquals($credit_number, $vendorCredit->credit_number);
        $this->assertEquals($purchase_order_number, $vendorCredit->purchase_order_number);
        $this->assertEquals($vendor_credit_date, $vendorCredit->vendor_credit_date);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $vendorCredit = VendorCredit::factory()->create();

        $response = $this->delete(route('vendor-credits.destroy', $vendorCredit));

        $response->assertNoContent();

        $this->assertModelMissing($vendorCredit);
    }
}
