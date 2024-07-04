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

        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('journal_entry_id')->constrained();
            $table->foreignId('journal_type_id')->constrained();
            $table->double('amount');
            $table->text('description');
            $table->string('account_id');
            $table->string('account_no');
            $table->string('created_by');
            $table->string('modified_by');
            $table->foreignId('deleted_by')->constrained('employees,ids', 'by');
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
        Schema::dropIfExists('journal_entry_details');
    }
};
