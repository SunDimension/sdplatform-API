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
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->unsignedBigInteger('create_item_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->float('quantity');
            $table->float('quantity_holding')->default(0);
            $table->float('cost_price');
            $table->float('selling_price');
            $table->string('reorder_level');
            $table->unsignedBigInteger('discount')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            // $table->float('open_stock')->default(0);
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
