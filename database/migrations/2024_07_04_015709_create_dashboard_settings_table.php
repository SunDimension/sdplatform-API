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

        Schema::create('dashboard_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('chart_id')->constrained();
            $table->foreignId('module_id')->constrained();
            $table->foreignId('chart_type_id')->constrained();
            $table->foreignId('chart_category_id')->constrained();
            $table->string('chart_title');
            $table->string('is_active');
            $table->string('order_by');
            $table->string('is_group');
            $table->string('submodule_Id');
            $table->string('add_condition');
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
        Schema::dropIfExists('dashboard_settings');
    }
};
