<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ApprovalStage;
use App\Models\DestinationBranch;
use App\Models\DestinationStore;
use App\Models\SourceBranch;
use App\Models\SourceStore;
use App\Models\StoreTransferOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StoreTransferOrderController
 */
final class StoreTransferOrderControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $storeTransferOrders = StoreTransferOrder::factory()->count(3)->create();

        $response = $this->get(route('store-transfer-orders.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StoreTransferOrderController::class,
            'store',
            \App\Http\Requests\StoreTransferOrderStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $order_number = $this->faker->word();
        $transfer_date = Carbon::parse($this->faker->dateTime());
        $source_branch = SourceBranch::factory()->create();
        $source_store = SourceStore::factory()->create();
        $destination_branch = DestinationBranch::factory()->create();
        $destination_store = DestinationStore::factory()->create();
        $approval_stage = ApprovalStage::factory()->create();
        $source_status = $this->faker->word();
        $destination_status = $this->faker->word();

        $response = $this->post(route('store-transfer-orders.store'), [
            'order_number' => $order_number,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'source_branch_id' => $source_branch->id,
            'source_store_id' => $source_store->id,
            'destination_branch_id' => $destination_branch->id,
            'destination_store_id' => $destination_store->id,
            'approval_stage_id' => $approval_stage->id,
            'source_status' => $source_status,
            'destination_status' => $destination_status,
        ]);

        $storeTransferOrders = StoreTransferOrder::query()
            ->where('order_number', $order_number)
            ->where('transfer_date', $transfer_date)
            ->where('source_branch_id', $source_branch->id)
            ->where('source_store_id', $source_store->id)
            ->where('destination_branch_id', $destination_branch->id)
            ->where('destination_store_id', $destination_store->id)
            ->where('approval_stage_id', $approval_stage->id)
            ->where('source_status', $source_status)
            ->where('destination_status', $destination_status)
            ->get();
        $this->assertCount(1, $storeTransferOrders);
        $storeTransferOrder = $storeTransferOrders->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $storeTransferOrder = StoreTransferOrder::factory()->create();

        $response = $this->get(route('store-transfer-orders.show', $storeTransferOrder));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StoreTransferOrderController::class,
            'update',
            \App\Http\Requests\StoreTransferOrderUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $storeTransferOrder = StoreTransferOrder::factory()->create();
        $order_number = $this->faker->word();
        $transfer_date = Carbon::parse($this->faker->dateTime());
        $source_branch = SourceBranch::factory()->create();
        $source_store = SourceStore::factory()->create();
        $destination_branch = DestinationBranch::factory()->create();
        $destination_store = DestinationStore::factory()->create();
        $approval_stage = ApprovalStage::factory()->create();
        $source_status = $this->faker->word();
        $destination_status = $this->faker->word();

        $response = $this->put(route('store-transfer-orders.update', $storeTransferOrder), [
            'order_number' => $order_number,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'source_branch_id' => $source_branch->id,
            'source_store_id' => $source_store->id,
            'destination_branch_id' => $destination_branch->id,
            'destination_store_id' => $destination_store->id,
            'approval_stage_id' => $approval_stage->id,
            'source_status' => $source_status,
            'destination_status' => $destination_status,
        ]);

        $storeTransferOrder->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($order_number, $storeTransferOrder->order_number);
        $this->assertEquals($transfer_date->timestamp, $storeTransferOrder->transfer_date);
        $this->assertEquals($source_branch->id, $storeTransferOrder->source_branch_id);
        $this->assertEquals($source_store->id, $storeTransferOrder->source_store_id);
        $this->assertEquals($destination_branch->id, $storeTransferOrder->destination_branch_id);
        $this->assertEquals($destination_store->id, $storeTransferOrder->destination_store_id);
        $this->assertEquals($approval_stage->id, $storeTransferOrder->approval_stage_id);
        $this->assertEquals($source_status, $storeTransferOrder->source_status);
        $this->assertEquals($destination_status, $storeTransferOrder->destination_status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $storeTransferOrder = StoreTransferOrder::factory()->create();

        $response = $this->delete(route('store-transfer-orders.destroy', $storeTransferOrder));

        $response->assertNoContent();

        $this->assertSoftDeleted($storeTransferOrder);
    }
}
