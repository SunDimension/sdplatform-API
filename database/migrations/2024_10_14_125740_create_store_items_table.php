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
       Schema::create('store_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained();
            $table->foreignId('create_item_id')->constrained('create_items');
            $table->foreignId('unit_id')->constrained('units');
            $table->float('quantity');
            $table->float('cost_price');
            $table->float('selling_price');
            $table->string('reorder_level');
            $table->foreignId('discount')->constrained('discounts');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_items');
    }
};
