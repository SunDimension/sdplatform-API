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

        Schema::create('property_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_id')->constrained();
            $table->string('survey_plan_url')->nullable();
            $table->string('title');
            $table->string('CofO')->nullable();
            $table->string('floor_plan')->nullable();
            $table->string('approval_letter')->nullable();
            $table->string('document_type');
            $table->boolean('verified')->default(false);
            $table->string('document_url');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_documents');
    }
};
