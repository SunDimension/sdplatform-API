// database/migrations/xxxx_create_branch_disbursed_stock_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_disbursed_stock', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('goods_recieved_items')->onDelete('cascade');
            $table->uuid('disbursement_id')->nullable();
            $table->foreign('disbursement_id')->references('disbursement_id')
                ->on('stock_disbursements')->onDelete('cascade');
            $table->decimal('quantity_disbursed', 15, 2)->default(0);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->decimal('quantity_available', 15, 2)->default(0); // disbursed - received
            $table->timestamps();

            // Composite index for faster lookups
            $table->index(['branch_id', 'product_id', 'disbursement_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_disbursed_stock');
    }
};
