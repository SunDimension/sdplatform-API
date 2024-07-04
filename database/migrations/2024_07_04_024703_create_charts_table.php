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

        Schema::create('charts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('chart_title');
            $table->foreignUuid('chart_type_id')->constrained();
            $table->foreignUuid('chart_category_id')->constrained();
            $table->text('sql_query');
            $table->string('is_active');
            $table->string('module_id');
            $table->string('filterColumn');
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
        Schema::dropIfExists('charts');
    }
};
