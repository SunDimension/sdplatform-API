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
        $tables = [
            'journal_entries',
            'transactions',
            'payment_vouchers',
            'customers',
            'vendors',
            'stores',
            'branches',
            'users',
            'sales',
            'purchases',
            'inventory_adjustments',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->uuid('sync_id')->nullable()->unique()->after('id');
                    $tableBlueprint->string('location_id')->nullable()->after('sync_id');
                    $tableBlueprint->enum('sync_status', ['pending', 'synced', 'failed'])->default('synced')->after('location_id');
                    $tableBlueprint->integer('sync_version')->default(1)->after('sync_status');
                    $tableBlueprint->timestamp('last_synced_at')->nullable()->after('sync_version');
                    $tableBlueprint->timestamp('last_sync_attempt_at')->nullable()->after('last_synced_at');
                    $tableBlueprint->text('sync_error')->nullable()->after('last_sync_attempt_at');

                    $tableBlueprint->index(['sync_status', 'last_synced_at']);
                    $tableBlueprint->index('location_id');
                    $tableBlueprint->index('sync_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'journal_entries',
            'transactions',
            'payment_vouchers',
            'customers',
            'vendors',
            'stores',
            'branches',
            'users',
            'sales',
            'purchases',
            'inventory_adjustments',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropIndex(['sync_status', 'last_synced_at']);
                    $tableBlueprint->dropIndex(['location_id']);
                    $tableBlueprint->dropIndex(['sync_id']);
                    
                    $tableBlueprint->dropColumn([
                        'sync_id',
                        'location_id',
                        'sync_status',
                        'sync_version',
                        'last_synced_at',
                        'last_sync_attempt_at',
                        'sync_error'
                    ]);
                });
            }
        }
    }
}; 