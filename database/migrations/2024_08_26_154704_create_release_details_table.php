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
        Schema::create('release_details', function (Blueprint $table) {
             $table->id();
            $table->foreignId('release_id')->constrained('releases')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('create_items')->onDelete('cascade');
            $table->string('release_quantity');
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_details');
    }
};
