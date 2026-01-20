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

        Schema::create('approval_process_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('name');
            $table->string('max_approval_count');
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->foreignId('modified_by')->nullable()->constrained('users', 'id');
            $table->foreignId('deleted_by')->nullable()->constrained('users', 'id');
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
        Schema::dropIfExists('approval_process_modules');
    }
};
