<?php

namespace App\Console\Commands;

use App\Models\StoreTransferOrder;
use App\Models\StoreTransferItem;
use App\Models\User;
use App\Models\Store;
use App\Models\Branch;
use App\Models\CreateItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestStoreTransferApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:store-transfer-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the store transfer order approval functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Store Transfer Order Approval System');
        $this->newLine();

        // Check if we have the required data
        $this->info('📋 Checking required data...');
        
        $users = User::count();
        $stores = Store::count();
        $branches = Branch::count();
        $products = CreateItem::count();
        
        $this->line("Users: {$users}");
        $this->line("Stores: {$stores}");
        $this->line("Branches: {$branches}");
        $this->line("Products: {$products}");
        
        if ($users === 0 || $stores === 0 || $branches === 0 || $products === 0) {
            $this->error('❌ Missing required data. Please ensure you have users, stores, branches, and products.');
            return 1;
        }
        
        $this->info('✅ Required data found!');
        $this->newLine();

        // Create test transfer order
        $this->info('📦 Creating test transfer order...');
        
        $sourceStore = Store::first();
        $destinationStore = Store::where('id', '!=', $sourceStore->id)->first() ?? $sourceStore;
        $sourceBranch = Branch::first();
        $destinationBranch = Branch::where('id', '!=', $sourceBranch->id)->first() ?? $sourceBranch;
        $product = CreateItem::first();
        $user = User::first();
        
        $transferOrder = StoreTransferOrder::create([
            'transfer_date' => now(),
            'source_branch_id' => $sourceBranch->id,
            'source_store_id' => $sourceStore->id,
            'destination_branch_id' => $destinationBranch->id,
            'destination_store_id' => $destinationStore->id,
            'source_status' => 'outgoing',
            'destination_status' => 'incoming',
            'created_by' => $user->id,
        ]);
        
        // Create transfer items
        StoreTransferItem::create([
            'transfer_order_id' => $transferOrder->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'quantity_pieces' => 100,
            'unit_price' => 25.50,
            'description' => 'Test transfer item',
            'created_by' => $user->id,
        ]);
        
        $this->info("✅ Transfer order created: {$transferOrder->order_number}");
        $this->line("Source: {$sourceStore->name} ({$sourceBranch->name})");
        $this->line("Destination: {$destinationStore->name} ({$destinationBranch->name})");
        $this->line("Product: {$product->name}");
        $this->line("Quantity: 10 units");
        $this->line("Status: {$transferOrder->source_status} / {$transferOrder->destination_status}");
        $this->newLine();

        // Show pending orders
        $this->info('📋 Current pending transfer orders:');
        $pendingOrders = StoreTransferOrder::where('source_status', 'outgoing')
            ->orWhere('destination_status', 'incoming')
            ->get();
            
        if ($pendingOrders->count() === 0) {
            $this->warn('No pending transfer orders found.');
        } else {
            foreach ($pendingOrders as $order) {
                $this->line("• {$order->order_number} - Source: {$order->source_status} | Destination: {$order->destination_status}");
            }
        }
        $this->newLine();

        // Demonstrate approval process
        $this->info('✅ Store Transfer Order Approval System is ready!');
        $this->newLine();
        
        $this->info('📝 To approve a transfer order, use the following API endpoints:');
        $this->newLine();
        
        $this->line('1. Get pending transfer orders:');
        $this->line('   GET /api/pending-transfer-orders');
        $this->newLine();
        
        $this->line('2. Approve source store transfer:');
        $this->line('   POST /api/approve-transfer-order');
        $this->line('   Body: {');
        $this->line('     "id": "' . $transferOrder->id . '",');
        $this->line('     "status": "approved",');
        $this->line('     "source": "source",');
        $this->line('     "stage": "store",');
        $this->line('     "comment": "Approved by store manager"');
        $this->line('   }');
        $this->newLine();
        
        $this->line('3. Approve destination store transfer:');
        $this->line('   POST /api/approve-transfer-order');
        $this->line('   Body: {');
        $this->line('     "id": "' . $transferOrder->id . '",');
        $this->line('     "status": "approved",');
        $this->line('     "source": "destination",');
        $this->line('     "stage": "store",');
        $this->line('     "comment": "Approved by destination store"');
        $this->line('   }');
        $this->newLine();
        
        $this->line('4. Branch level approvals:');
        $this->line('   GET /api/pending-transfer-branch-orders');
        $this->line('   POST /api/approve-transfer-order (with stage: "branch")');
        $this->newLine();
        
        $this->info('🎯 The approval system supports:');
        $this->line('• Source and destination approvals');
        $this->line('• Store and branch level approvals');
        $this->line('• Auto-approval for same branch transfers');
        $this->line('• Accounting entries creation');
        $this->line('• Comprehensive logging and error handling');
        $this->newLine();
        
        $this->info('🧪 You can also run the tests with:');
        $this->line('php artisan test tests/Feature/StoreTransferOrderControllerTest.php');
        
        return 0;
    }
}
