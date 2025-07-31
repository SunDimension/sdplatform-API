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
        Schema::create('return_details', function (Blueprint $table) {
          $table->id();
            $table->unsignedBigInteger('return_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('return_quantity');
            $table->integer('return_quantity_pieces');
            $table->unsignedBigInteger('item_sold_id')->nullable();
            $table->float('unit_price');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->bigInteger('unit_measurement');
            // $table->text('notes')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_details');
    }
};
