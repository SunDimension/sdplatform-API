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

        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agency_id')->nullable()->constrained();
            $table->foreignUuid('owner_id')->constrained('users');
            $table->foreignUuid('agent_id')->nullable()->constrained('users');
            $table->foreignUuid('property_type_id')->constrained();
            $table->foreignUuid('category_id')->constrained('property_categories');
            $table->foreignUuid('status_id')->constrained('property_statuses');
            $table->string('purpose');
            $table->text('description');
            $table->float('price');
            $table->string('title');
            $table->string('currency');
            $table->boolean('negotiable')->default(false);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('toilets')->nullable();
            $table->integer('parking_spaces')->nullable();
            $table->integer('kitchen')->nullable();
            $table->integer('living_rooms')->nullable();
            $table->float('land_size')->nullable();
            $table->float('building_size')->nullable();
            $table->integer('year_built')->nullable();
            $table->integer('floors')->nullable();
            $table->boolean('furnished')->default(false);
            $table->boolean('serviced')->default(false);
            $table->boolean('pet_friendly')->default(false);
            $table->string('minimum_rent_period')->nullable();
            $table->date('available_from')->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->foreignUuid('country_id')->constrained();
            $table->foreignUuid('state_id')->constrained();
            $table->foreignUuid('city_id')->constrained();
            $table->foreignUuid('area_id')->nullable()->constrained();
            $table->string('street_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('premium')->default(false);
            $table->boolean('verified')->default(false);
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
