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

        Schema::create('new_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained();
            $table->foreignId('item_id')->constrained('create_items');
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('payment_mode_id')->constrained();
            $table->string('purchase_order_number');
            $table->string('purchase_amount');
            $table->timestamp('purchase_date');
            $table->date('expected_delivery_date');
            $table->foreignId('payment_type_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_purchase_orders');
    }
};
