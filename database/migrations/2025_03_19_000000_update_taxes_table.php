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
        // Taxes table doesn't exist yet
        // Schema::table('taxes', function (Blueprint $table) {
        //     $table->decimal('rate', 5, 2)->default(0);
        //     $table->string('type')->default('vat'); // 'vat' or 'wht'
        //     $table->boolean('is_active')->default(true);
        //     $table->text('description')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn(['rate', 'type', 'is_active', 'description']);
        });
    }
}; 