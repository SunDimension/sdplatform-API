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

        Schema::create('reported_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_id')->constrained();
            $table->foreignUuid('reporter_id')->constrained('users');
            $table->text('reason');
            $table->text('description')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users', 'by');
            $table->timestamp('resolved_at')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reported_listings');
    }
};
