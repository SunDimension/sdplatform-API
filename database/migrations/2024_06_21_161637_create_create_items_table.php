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

        Schema::create('create_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('item_category_id')->constrained();
            $table->foreignId('item_type_id');
            $table->string('description');
            $table->string('batch_number')->unique();
            $table->foreignId('unit_id')->constrained();
            $table->foreignId('brand_id')->constrained();
            $table->float('cost_price');
            $table->float('selling_price');
            $table->string('reorder_level');
            $table->foreignId('dimension_id')->constrained();
            $table->foreignId('weight_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('warehouse')->constrained('warehouses');
            $table->foreignId('vendor_id')->constrained();
            $table->string('image_url');
            $table->string('barcode');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_items');
    }
};
