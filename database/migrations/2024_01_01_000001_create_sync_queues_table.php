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
        Schema::create('sync_queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sync_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->enum('action', ['create', 'update', 'delete']);
            $table->json('data');
            $table->string('location_id');
            $table->integer('priority')->default(5);
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->enum('status', ['pending', 'processing', 'failed', 'completed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->uuid('sync_batch_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'next_attempt_at']);
            $table->index(['location_id', 'status']);
            $table->index(['model_type', 'model_id']);
            $table->index('sync_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queues');
    }
}; 