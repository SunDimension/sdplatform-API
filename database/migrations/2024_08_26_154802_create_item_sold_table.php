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
        Schema::create('item_sold', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('create_item')->onDelete('cascade');
            $table->foreignId('sales_order_id')->constrained('sales_order')->onDelete('cascade');
            $table->string('quantity');
            $table->string('unit_price');
            $table->string('amount');
            $table->date('sales_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sold');
    }
};
