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

        Schema::create('transaction_journal_entry_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_journal_entry_id')->constrained('transaction_journal_entries', 'id', 'tje_details_entry_id_foreign');
            $table->uuid('journal_type_id');
            $table->double('amount');
            $table->text('description');
            $table->uuid('account_id');
            $table->string('account_no');
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
        Schema::dropIfExists('transaction_journal_entry_details');
    }
};
