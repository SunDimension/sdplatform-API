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

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('customer_id')->constrained('customersses');
            $table->string('invoice_number');
            $table->string('order_number');
            $table->timestamp('invoice_date');
            $table->foreignId('item_id')->constrained('create_items');
            $table->string('rate');
            $table->string('quantity');
            $table->foreignId('discount_id')->constrained();
            $table->foreignId('tax_id')->constrained();
            $table->string('amount');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
