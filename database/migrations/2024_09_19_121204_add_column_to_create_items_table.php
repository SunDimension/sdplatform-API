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
            $table->integer("store_id")->constrained("stores");
            $table->integer("user_id")->constrained("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('create_items', function (Blueprint $table) {
            $table->dropColumn("store_id");//->constrained("stores");
            $table->dropColumn("user_id");//->constrained("stores");
        });
    }
};
