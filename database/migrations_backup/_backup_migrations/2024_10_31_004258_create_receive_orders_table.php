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

        Schema::create('receive_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purchase_order_number')->nullable();
            $table->date('receive_date');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('store_id')->constrained();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->timestamp('approval_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->string('approval_comment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('modified_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('receive_orders');
    }
};
