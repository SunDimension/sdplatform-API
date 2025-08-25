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
        // Add sync columns to journal_entries table
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to payment_vouchers table
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to stores table
        Schema::table('stores', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });

        // Add sync columns to branches table
        Schema::table('branches', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->string('sync_id')->nullable()->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sync columns from journal_entries table
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from payment_vouchers table
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from stores table
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });

        // Remove sync columns from branches table
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_id', 'last_synced_at', 'sync_metadata']);
        });
    }
};
