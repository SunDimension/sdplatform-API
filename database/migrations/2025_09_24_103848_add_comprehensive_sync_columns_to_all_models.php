<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Core financial models
        $this->addSyncColumnsToTable('journal_entries');
        $this->addSyncColumnsToTable('journal_entry_details');
        $this->addSyncColumnsToTable('transactions');
        $this->addSyncColumnsToTable('payment_vouchers');
        // $this->addSyncColumnsToTable('transaction_journal_entries');
        // $this->addSyncColumnsToTable('transaction_journal_entry_details');
        
        // Inventory and product models
        $this->addSyncColumnsToTable('create_items');
        $this->addSyncColumnsToTable('store_items');
        $this->addSyncColumnsToTable('stores');
        $this->addSyncColumnsToTable('branches');
        
        // Customer and vendor models
        $this->addSyncColumnsToTable('customers');
        $this->addSyncColumnsToTable('vendors');
        
        // Sales models
        $this->addSyncColumnsToTable('sales_orders');
        $this->addSyncColumnsToTable('sales_receipts');
        $this->addSyncColumnsToTable('item_solds');
        
        // Purchase and receiving models
        $this->addSyncColumnsToTable('receive_orders');
        $this->addSyncColumnsToTable('receive_items');
        
        // Transfer models
        $this->addSyncColumnsToTable('store_transfer_orders');
        $this->addSyncColumnsToTable('store_transfer_items');
        
        // Financial flow models
        $this->addSyncColumnsToTable('bank_remittances');
        $this->addSyncColumnsToTable('post_outflows');
        $this->addSyncColumnsToTable('post_inflows');
        $this->addSyncColumnsToTable('credit_transactions');
        
        // Release and return models
        $this->addSyncColumnsToTable('releases');
        $this->addSyncColumnsToTable('release_details');
        $this->addSyncColumnsToTable('return_items');
        $this->addSyncColumnsToTable('return_details');
        
        // Price and expense models
        $this->addSyncColumnsToTable('price_changes');
        $this->addSyncColumnsToTable('cashier_remittances');
        $this->addSyncColumnsToTable('cashier_expenses');
        
        // User model
        $this->addSyncColumnsToTable('users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Core financial models
        $this->removeSyncColumnsFromTable('journal_entries');
        $this->removeSyncColumnsFromTable('journal_entry_details');
        $this->removeSyncColumnsFromTable('transactions');
        $this->removeSyncColumnsFromTable('payment_vouchers');
        $this->removeSyncColumnsFromTable('transaction_journal_entries');
        $this->removeSyncColumnsFromTable('transaction_journal_entry_details');
        
        // Inventory and product models
        $this->removeSyncColumnsFromTable('create_items');
        $this->removeSyncColumnsFromTable('store_items');
        $this->removeSyncColumnsFromTable('stores');
        $this->removeSyncColumnsFromTable('branches');
        
        // Customer and vendor models
        $this->removeSyncColumnsFromTable('customers');
        $this->removeSyncColumnsFromTable('vendors');
        
        // Sales models
        $this->removeSyncColumnsFromTable('sales_orders');
        $this->removeSyncColumnsFromTable('sales_receipts');
        $this->removeSyncColumnsFromTable('item_solds');
        
        // Purchase and receiving models
        $this->removeSyncColumnsFromTable('receive_orders');
        $this->removeSyncColumnsFromTable('receive_items');
        
        // Transfer models
        $this->removeSyncColumnsFromTable('store_transfer_orders');
        $this->removeSyncColumnsFromTable('store_transfer_items');
        
        // Financial flow models
        $this->removeSyncColumnsFromTable('bank_remittances');
        $this->removeSyncColumnsFromTable('post_outflows');
        $this->removeSyncColumnsFromTable('post_inflows');
        $this->removeSyncColumnsFromTable('credit_transactions');
        
        // Release and return models
        $this->removeSyncColumnsFromTable('releases');
        $this->removeSyncColumnsFromTable('release_details');
        $this->removeSyncColumnsFromTable('return_items');
        $this->removeSyncColumnsFromTable('return_details');
        
        // Price and expense models
        $this->removeSyncColumnsFromTable('price_changes');
        $this->removeSyncColumnsFromTable('cashier_remittances');
        $this->removeSyncColumnsFromTable('cashier_expenses');
        
        // User model
        $this->removeSyncColumnsFromTable('users');
    }

    /**
     * Add sync columns to a table
     */
    private function addSyncColumnsToTable(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'sync_id')) {
                $table->uuid('sync_id')->nullable()->unique();
            }
            if (!Schema::hasColumn($tableName, 'location_id')) {
                $table->string('location_id')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'sync_status')) {
                $table->string('sync_status')->default('pending')->index();
            }
            if (!Schema::hasColumn($tableName, 'sync_version')) {
                $table->integer('sync_version')->default(1);
            }
            if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'last_sync_attempt_at')) {
                $table->timestamp('last_sync_attempt_at')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'sync_error')) {
                $table->text('sync_error')->nullable();
            }
            if (!Schema::hasColumn($tableName, 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Remove sync columns from a table
     */
    private function removeSyncColumnsFromTable(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn([
                'sync_id',
                'location_id', 
                'sync_status',
                'sync_version',
                'last_synced_at',
                'last_sync_attempt_at',
                'sync_error',
                'deleted_at'
            ]);
        });
    }
};
