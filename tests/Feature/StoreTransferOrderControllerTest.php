<?php

namespace Tests\Feature;

use App\Models\StoreTransferOrder;
use App\Models\StoreTransferItem;
use App\Models\User;
use App\Models\Store;
use App\Models\Branch;
use App\Models\CreateItem;
use App\Services\StoreTransferApprovalService;
use App\Services\AccountingEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class StoreTransferOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $store;
    protected $branch;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->store = Store::factory()->create();
        $this->branch = Branch::factory()->create();
        $this->product = CreateItem::factory()->create();
    }

    /** @test */
    public function it_can_approve_source_store_transfer()
    {
        // Create a store transfer order
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'outgoing',
            'destination_status' => 'incoming',
            'source_store_id' => $this->store->id,
            'source_branch_id' => $this->branch->id,
            'destination_store_id' => $this->store->id,
            'destination_branch_id' => $this->branch->id,
        ]);

        // Create transfer items
        StoreTransferItem::factory()->create([
            'transfer_order_id' => $transferOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'store',
                'comment' => 'Approved by store manager'
            ]);

        $response->assertStatus(200);
        
        // Assert the transfer order was updated
        $this->assertDatabaseHas('store_transfer_orders', [
            'id' => $transferOrder->id,
            'source_status' => 'approved',
            'source_store_approval_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_approve_destination_store_transfer()
    {
        // Create a store transfer order
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'approved',
            'destination_status' => 'incoming',
            'source_store_id' => $this->store->id,
            'source_branch_id' => $this->branch->id,
            'destination_store_id' => $this->store->id,
            'destination_branch_id' => $this->branch->id,
        ]);

        // Create transfer items
        StoreTransferItem::factory()->create([
            'transfer_order_id' => $transferOrder->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'destination',
                'stage' => 'store',
                'comment' => 'Approved by destination store'
            ]);

        $response->assertStatus(200);
        
        // Assert the transfer order was updated
        $this->assertDatabaseHas('store_transfer_orders', [
            'id' => $transferOrder->id,
            'destination_status' => 'approved',
            'destination_store_approval_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_auto_approves_when_same_branch_and_pending()
    {
        // Create a store transfer order with same branch
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'approved',
            'destination_status' => 'incoming',
            'source_store_id' => $this->store->id,
            'source_branch_id' => $this->branch->id,
            'destination_store_id' => $this->store->id,
            'destination_branch_id' => $this->branch->id, // Same branch
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'pending',
                'source' => 'destination',
                'stage' => 'store',
                'comment' => 'Auto-approved due to same branch'
            ]);

        $response->assertStatus(200);
        
        // Should be auto-approved to 'approved'
        $this->assertDatabaseHas('store_transfer_orders', [
            'id' => $transferOrder->id,
            'destination_status' => 'approved',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id', 'status', 'source', 'stage']);
    }

    /** @test */
    public function it_validates_status_values()
    {
        $transferOrder = StoreTransferOrder::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'invalid_status',
                'source' => 'source',
                'stage' => 'store',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_validates_source_values()
    {
        $transferOrder = StoreTransferOrder::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'invalid_source',
                'stage' => 'store',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['source']);
    }

    /** @test */
    public function it_validates_stage_values()
    {
        $transferOrder = StoreTransferOrder::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'invalid_stage',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['stage']);
    }

    /** @test */
    public function it_returns_error_for_nonexistent_transfer_order()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => 'non-existent-id',
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'store',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function it_returns_error_for_wrong_status_transfer_order()
    {
        // Create transfer order with wrong status for source approval
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'approved', // Should be 'outgoing' for source approval
            'destination_status' => 'incoming',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'store',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function it_handles_comment_field()
    {
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'outgoing',
            'destination_status' => 'incoming',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'store',
                'comment' => 'This is a test comment for approval'
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_comment_length()
    {
        $transferOrder = StoreTransferOrder::factory()->create([
            'source_status' => 'outgoing',
            'destination_status' => 'incoming',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/store-transfer-orders/approve', [
                'id' => $transferOrder->id,
                'status' => 'approved',
                'source' => 'source',
                'stage' => 'store',
                'comment' => str_repeat('a', 1001), // Exceeds 1000 character limit
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['comment']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 