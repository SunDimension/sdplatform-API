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

        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('create_items');
            $table->timestamp('expense_date');
            $table->string('amount');
            $table->string('description');
            $table->foreignId('branch_id')->constrained();

            $table->foreignId('tax_id')->constrained();
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('payment_mode_id')->constrained();
            $table->foreignId('expense_account_id')->constrained('expense_account_ids');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
