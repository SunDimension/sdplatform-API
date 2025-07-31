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
        Schema::disableForeignKeyConstraints();

        Schema::create('store_transfer_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number');
            $table->timestamp('transfer_date');
            $table->unsignedBigInteger('source_branch_id')->nullable();
            $table->unsignedBigInteger('source_store_id')->nullable();
            $table->unsignedBigInteger('destination_branch_id')->nullable();
            $table->unsignedBigInteger('destination_store_id')->nullable();
            // $table->unsignedBigInteger('approval_stage_id')->nullable();
            $table->enum('source_status', ['outgoing','pending', 'approved','declined', 'cancelled'])->default('outgoing');
            $table->timestamp('source_store_date_approved')->nullable();
            $table->timestamp('source_branch_date_approved')->nullable();
            $table->enum('destination_status', ['incoming','pending', 'approved','declined', 'cancelled'])->default('incoming');
            $table->timestamp('destination_store_date_approved')->nullable();
            $table->timestamp('destination_branch_date_approved')->nullable();
            
            $table->unsignedBigInteger('source_store_approval_by')->nullable();
            $table->unsignedBigInteger('source_branch_approval_by')->nullable();
            $table->unsignedBigInteger('destination_store_approval_by')->nullable();
            $table->unsignedBigInteger('destination_branch_approval_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_transfer_orders');
    }
};
