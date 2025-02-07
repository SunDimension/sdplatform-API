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
        Schema::create('price_changes', function (Blueprint $table) {
            $table->id();
            $table->json('details');
            $table->string('store_id');
            $table->string('branch_id');
            $table->string('change_reason_id');
            $table->enum('status', ["pending","approved","declined"])->default('pending');
            $table->string('approved_by')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->string('comment');
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_changes');
    }
};
