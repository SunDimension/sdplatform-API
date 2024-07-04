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

        Schema::create('account_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('account_group_id')->constrained();
            $table->string('name');
            $table->string('code');
            $table->foreignId('created_by')->constrained('employees,ids', 'by');
            $table->foreignId('modified_by')->constrained('employees,ids', 'by');
            $table->foreignId('deleted_by')->constrained('employees,ids', 'by');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
