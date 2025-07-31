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
        Schema::table('journal_entries', function (Blueprint $table) {
            // Drop the warehouse_id foreign key constraint
            $table->dropForeign(['warehouse_id']);
            // Drop the warehouse_id column
            $table->dropColumn('warehouse_id');
            // Add the store_id column with foreign key constraint
            $table->foreignId('store_id')->constrained('stores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            // Drop the store_id foreign key constraint
            $table->dropForeign(['store_id']);
            // Drop the store_id column
            $table->dropColumn('store_id');
            // Add back the warehouse_id column with foreign key constraint
            $table->foreignId('warehouse_id')->constrained('warehouses');
        });
    }
}; 