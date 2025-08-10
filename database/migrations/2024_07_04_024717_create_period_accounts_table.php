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
            $table->foreignUuid('financial_period_id')->constrained();
            $table->double('debit');
            $table->double('credit');
            $table->double('amount');

            $table->string('account_no')->nullable();
            $table->foreignUuid('account_id')->constrained();
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
        Schema::dropIfExists('period_accounts');
    }
};
