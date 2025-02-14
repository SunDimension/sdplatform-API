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

        Schema::create('receive_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('receive_order_id')->constrained();
            $table->integer('quantity');
            $table->double('unit_price');
            $table->foreignId('product_id')->constrained('create_items','id');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('modified_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('receive_items');
    }
};
