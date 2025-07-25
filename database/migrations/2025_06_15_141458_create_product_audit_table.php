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
        // Create migration
        Schema::create('product_audits', function (Blueprint $table) {
            $table->id();
            $table->string('action_type'); // replenished, sold, returned, etc.
            $table->foreignId('product_id')->constrained('create_items');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('quantity_change', 10, 2)->nullable();
            $table->decimal('previous_quantity', 10, 2)->nullable();
            $table->decimal('new_quantity', 10, 2)->nullable();
            $table->decimal('price_change', 10, 2)->nullable();
            $table->decimal('previous_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2)->nullable();
            $table->string('reference_type')->nullable(); // Order, Return, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_audit');
    }
};
