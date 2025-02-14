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

        Schema::create('financial_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->date('date_from');
            $table->date('date_to');
            $table->boolean('is_active');
            $table->foreignUuid('financial_year_id')->constrained();
            $table->foreignUuid('financial_quarter_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('modified_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_periods');
    }
};
