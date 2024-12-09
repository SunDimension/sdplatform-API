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

        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('sales_order_id')->nullable()->constrained();
            $table->foreignId('sales_receipt_id')->nullable()->constrained();
            $table->string('amount');
            $table->string('credit_limit')->nullable();
            $table->string('credit_balance_before')->nullable();
            $table->enum('type', ["credit","payment"]);
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('modified_by')->nullable()->constrained('users', 'id');
            $table->foreignId('deleted_by')->nullable()->constrained('users', 'id');
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
        Schema::dropIfExists('credit_transactions');
    }
};
