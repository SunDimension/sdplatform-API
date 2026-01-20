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
        Schema::create('periodic_financial_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('report_type', ['profit_loss', 'balance_sheet', 'trial_balance']);
            $table->uuid('financial_period_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->json('report_data');
            $table->timestamp('generated_at');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->boolean('is_balanced')->default(false);
            $table->decimal('total_debits', 15, 2)->default(0);
            $table->decimal('total_credits', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final', 'archived'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('financial_period_id')->references('id')->on('financial_periods')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['report_type', 'financial_period_id']);
            $table->index(['store_id', 'financial_period_id']);
            $table->index(['branch_id', 'financial_period_id']);
            $table->index(['region_id', 'financial_period_id']);
            $table->index(['status', 'generated_at']);
            $table->index(['report_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_financial_reports');
    }
}; 