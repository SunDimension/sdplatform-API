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

        Schema::create('purchase_received_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('new_purchased_received_id')->constrained('new_purchase_receiveds');
            $table->foreignId('item_category_id')->constrained();
            $table->foreignId('item_id')->constrained('create_items');
            $table->string('unit_price');
            $table->string('quantity');
            $table->foreignId('unit_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_received_details');
    }
};
