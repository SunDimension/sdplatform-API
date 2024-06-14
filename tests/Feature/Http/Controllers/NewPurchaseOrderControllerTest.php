<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\NewPurchaseOrder;
use App\Models\PaymentMode;
use App\Models\PaymentType;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NewPurchaseOrderController
 */
final class NewPurchaseOrderControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $newPurchaseOrders = NewPurchaseOrder::factory()->count(3)->create();

        $response = $this->get(route('new-purchase-orders.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseOrderController::class,
            'store',
            \App\Http\Requests\NewPurchaseOrderStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $item_category = ItemCategory::factory()->create();
        $item = Item::factory()->create();
        $vendor = Vendor::factory()->create();
        $branch = Branch::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $purchase_order_number = $this->faker->word();
        $purchase_amount = $this->faker->word();
        $purchase_date = Carbon::parse($this->faker->dateTime());
        $expected_delivery_date = Carbon::parse($this->faker->date());
        $payment_type = PaymentType::factory()->create();

        $response = $this->post(route('new-purchase-orders.store'), [
            'item_category_id' => $item_category->id,
            'item_id' => $item->id,
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'payment_mode_id' => $payment_mode->id,
            'purchase_order_number' => $purchase_order_number,
            'purchase_amount' => $purchase_amount,
            'purchase_date' => $purchase_date->toDateTimeString(),
            'expected_delivery_date' => $expected_delivery_date->toDateString(),
            'payment_type_id' => $payment_type->id,
        ]);

        $newPurchaseOrders = NewPurchaseOrder::query()
            ->where('item_category_id', $item_category->id)
            ->where('item_id', $item->id)
            ->where('vendor_id', $vendor->id)
            ->where('branch_id', $branch->id)
            ->where('payment_mode_id', $payment_mode->id)
            ->where('purchase_order_number', $purchase_order_number)
            ->where('purchase_amount', $purchase_amount)
            ->where('purchase_date', $purchase_date)
            ->where('expected_delivery_date', $expected_delivery_date)
            ->where('payment_type_id', $payment_type->id)
            ->get();
        $this->assertCount(1, $newPurchaseOrders);
        $newPurchaseOrder = $newPurchaseOrders->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $newPurchaseOrder = NewPurchaseOrder::factory()->create();

        $response = $this->get(route('new-purchase-orders.show', $newPurchaseOrder));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NewPurchaseOrderController::class,
            'update',
            \App\Http\Requests\NewPurchaseOrderUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $newPurchaseOrder = NewPurchaseOrder::factory()->create();
        $item_category = ItemCategory::factory()->create();
        $item = Item::factory()->create();
        $vendor = Vendor::factory()->create();
        $branch = Branch::factory()->create();
        $payment_mode = PaymentMode::factory()->create();
        $purchase_order_number = $this->faker->word();
        $purchase_amount = $this->faker->word();
        $purchase_date = Carbon::parse($this->faker->dateTime());
        $expected_delivery_date = Carbon::parse($this->faker->date());
        $payment_type = PaymentType::factory()->create();

        $response = $this->put(route('new-purchase-orders.update', $newPurchaseOrder), [
            'item_category_id' => $item_category->id,
            'item_id' => $item->id,
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'payment_mode_id' => $payment_mode->id,
            'purchase_order_number' => $purchase_order_number,
            'purchase_amount' => $purchase_amount,
            'purchase_date' => $purchase_date->toDateTimeString(),
            'expected_delivery_date' => $expected_delivery_date->toDateString(),
            'payment_type_id' => $payment_type->id,
        ]);

        $newPurchaseOrder->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($item_category->id, $newPurchaseOrder->item_category_id);
        $this->assertEquals($item->id, $newPurchaseOrder->item_id);
        $this->assertEquals($vendor->id, $newPurchaseOrder->vendor_id);
        $this->assertEquals($branch->id, $newPurchaseOrder->branch_id);
        $this->assertEquals($payment_mode->id, $newPurchaseOrder->payment_mode_id);
        $this->assertEquals($purchase_order_number, $newPurchaseOrder->purchase_order_number);
        $this->assertEquals($purchase_amount, $newPurchaseOrder->purchase_amount);
        $this->assertEquals($purchase_date->timestamp, $newPurchaseOrder->purchase_date);
        $this->assertEquals($expected_delivery_date, $newPurchaseOrder->expected_delivery_date);
        $this->assertEquals($payment_type->id, $newPurchaseOrder->payment_type_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $newPurchaseOrder = NewPurchaseOrder::factory()->create();

        $response = $this->delete(route('new-purchase-orders.destroy', $newPurchaseOrder));

        $response->assertNoContent();

        $this->assertModelMissing($newPurchaseOrder);
    }
}
