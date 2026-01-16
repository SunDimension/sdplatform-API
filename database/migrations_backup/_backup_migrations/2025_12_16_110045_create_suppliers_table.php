<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplier_id');
            $table->string('supplier_code')->unique();
            $table->string('supplier_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_acct_name')->nullable();
            $table->string('bank_acct_num')->nullable();
            $table->string('payment_terms')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Sync fields
            $table->string('sync_id')->nullable()->unique();
            $table->string('sync_location')->nullable();
            $table->integer('sync_version')->default(0);
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_sync_attempt')->nullable();
            $table->text('sync_error')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('supplier_code');
            $table->index('status');
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};