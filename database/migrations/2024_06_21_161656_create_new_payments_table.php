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

        Schema::create('new_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->string('payment_amount');
            $table->foreignId('payment_mode_id')->constrained();
            $table->string('description');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_payments');
    }
};
