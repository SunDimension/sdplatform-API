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
        Schema::create('cashier_remittance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('cash_discrepancy_id')->constrained('cash_discrepancies');
            $table->float('amount');
            $table->float('discrepancy_amount');
             $table->timestamps('approval_date');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('approved_by')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_remittance');
    }
};
