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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->integer('approval_stage_id')->nullable()->constrained('approval_stages');
            $table->integer('approval_officer_id')->nullable()->constrained('users');
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->integer('approval_stage_id')->nullable()->constrained('approval_stages');
            $table->integer('approval_officer_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn('approval_stage_id');
            $table->dropColumn('approval_officer_id');
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn('approval_stage_id');
            $table->dropColumn('approval_officer_id');
        });
    }
};
