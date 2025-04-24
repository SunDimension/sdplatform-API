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
            $table->foreignId('return_id')->constrained('return_items','id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('create_items','id')->onDelete('cascade');
            $table->integer('return_quantity');
            $table->integer('return_quantity_pieces');
            $table->foreignId('item_sold_id')->constrained('item_solds','id')->onDelete('cascade');
            $table->float('unit_price');
            $table->foreignId('store_id')->constrained('stores','id')->onDelete('cascade');
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
