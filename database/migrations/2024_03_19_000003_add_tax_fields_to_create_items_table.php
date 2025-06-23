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
        Schema::table('create_items', function (Blueprint $table) {
            $table->foreignId('tax_id')->nullable()->constrained('taxes');
            $table->boolean('is_tax_inclusive')->default(false);
            $table->decimal('tax_amount', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('create_items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['tax_id', 'is_tax_inclusive', 'tax_amount']);
        });
    }
}; 