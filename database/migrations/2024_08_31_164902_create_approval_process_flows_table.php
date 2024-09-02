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

        Schema::create('approval_process_flows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sequence_no');
            $table->foreignUuid('process_module_id')->constrained('approval_process_modules');
            $table->foreignUuid('approval_stage_id')->constrained();
            $table->string('status_id');
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
        Schema::dropIfExists('approval_process_flows');
    }
};
