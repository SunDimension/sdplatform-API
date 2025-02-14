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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->enum('payment_type', ['Cash','Bank','Paylater','Credit'])->default('Cash');
            $table->enum('status', ['Pending','Approved','Paid','Cancelled'])->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('payment_type');
            $table->dropColumn('status');
        });
    }
};
