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
        Schema::create('sales_receipts', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('sales_invoice_id')->nullable();
             $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
             $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('payment_mode_id')->nullable();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->string('sales_receipt_number')->unique();
            $table->date('receipt_date');
            $table->string('amount_paid');
            $table->string('total_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_receipts');
    }
};
