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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'sync_id')) {
                $table->uuid('sync_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('stores', 'location_id')) {
                $table->string('location_id')->nullable();
            }
            if (!Schema::hasColumn('stores', 'sync_status')) {
                $table->string('sync_status')->default('pending')->index();
            }
            if (!Schema::hasColumn('stores', 'sync_version')) {
                $table->integer('sync_version')->default(1);
            }
            if (!Schema::hasColumn('stores', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable();
            }
            if (!Schema::hasColumn('stores', 'last_sync_attempt_at')) {
                $table->timestamp('last_sync_attempt_at')->nullable();
            }
            if (!Schema::hasColumn('stores', 'sync_error')) {
                $table->text('sync_error')->nullable();
            }
            if (!Schema::hasColumn('stores', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'sync_id',
                'location_id', 
                'sync_status',
                'sync_version',
                'last_synced_at',
                'last_sync_attempt_at',
                'sync_error',
                'deleted_at'
            ]);
        });
    }
};
