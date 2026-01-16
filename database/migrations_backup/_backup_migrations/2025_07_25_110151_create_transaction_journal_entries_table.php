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

        Schema::create('transaction_journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->timestamp('payment_date');
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('vendor_id');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('modified_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->integer('approval_stage_id')->nullable();
            $table->integer('approval_officer_id')->nullable();
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
        Schema::dropIfExists('transaction_journal_entries');
    }
};
