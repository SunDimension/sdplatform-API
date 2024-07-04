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

        Schema::create('period_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('financial_period_id')->constrained();
            $table->double('debit');
            $table->double('credit');
            $table->double('amount');
            $table->foreignId('warehouse_id')->constrained();
            $table->string('account_no');
            $table->foreignId('account_id')->constrained('accounts,ids');
            $table->foreignId('created_by')->constrained('employees,ids', 'by');
            $table->foreignId('modified_by')->constrained('employees,ids', 'by');
            $table->foreignId('deleted_by')->constrained('employees,ids', 'by');
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
        Schema::dropIfExists('period_accounts');
    }
};
