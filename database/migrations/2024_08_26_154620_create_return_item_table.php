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
        Schema::create('return_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->constrained('release')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_receipt_id')->constrained('sales_receipt')->onDelete('cascade');
            $table->date('return_date');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_item');
    }
};
