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

        Schema::create('agencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('registeration_number');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('status');
            $table->string('website')->nullable();
            $table->string('license_number')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('subscription_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
