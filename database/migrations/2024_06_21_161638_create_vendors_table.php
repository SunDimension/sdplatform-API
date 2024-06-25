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

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('contact_title');
            $table->string('contact_designation');
            $table->string('contact_surname');
            $table->string('contact_firstname');
            $table->string('contact_middlename');
            $table->string('contact_fullname');
            $table->foreignId('vendor_type_id')->constrained();
            $table->string('phone_number');
            $table->string('email');
            $table->string('image_url');
            $table->string('tin');
            $table->foreignId('bank_id');
            $table->string('account_number');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
