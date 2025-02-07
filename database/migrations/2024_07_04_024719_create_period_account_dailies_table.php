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

        Schema::create('period_account_dailies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('period_date');
            $table->double('debit');
            $table->double('credit');
            $table->double('amount');
            $table->foreignId('warehouse_id')->constrained();
            $table->string('account_no');
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
        Schema::dropIfExists('period_account_dailies');
    }
};
