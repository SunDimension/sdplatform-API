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

        Schema::create('ad_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->float('price');
            $table->integer('duration');
            $table->boolean('homepage')->default(false);
            $table->boolean('category_page')->default(false);
            $table->boolean('search_page')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_packages');
    }
};
