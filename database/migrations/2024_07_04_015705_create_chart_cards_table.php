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

        Schema::create('chart_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('card_title');
            $table->string('card_size');
            $table->string('is_active');
            $table->text('sql_query');
            $table->string('module_id');
            $table->string('submodule_id');
            $table->string('sequence');
            $table->string('color');
            $table->foreignId('created_by')->constrained('employees,ids', 'by');
            $table->foreignId('modified_by')->constrained('employees,ids', 'by');
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
        Schema::dropIfExists('chart_cards');
    }
};
