<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Note: Laravel does not directly support modifying ENUM columns.
        // You can use raw SQL queries to accomplish this.
        // DB::statement("ALTER TABLE sales_receipt MODIFY `payment_type` ENUM('Cash','Bank','Deposit','POS','Multi-Payment') NOT NULL;");
        Schema::table('sales_receipts', function (Blueprint $table) {
            $table->json('payment_detail');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    
    public function down()
    {
        // Revert to the original ENUM values if necessary
        DB::statement("ALTER TABLE sales_receipts MODIFY payment_type varchar(300);");
        Schema::table('sales_receipts', function (Blueprint $table) {
            $table->dropColumn('payment_detail');
        });
    }
};
