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
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE release_daily_holding_quantity()
            BEGIN
            UPDATE store_items si join (
            SELECT `is`.product_id, SUM(`is`.quantity) total_hold, `is`.store_id FROM item_solds `is`
            WHERE `is`.sales_order_id IN (SELECT id FROM sales_orders so 
            WHERE status ='Pending' AND date(so.created_at) = date(CURDATE())
            GROUP BY product_id, `is`.store_id) A ON A.product_id = si.create_item_id AND A.store_id = si.store_id
            SET si.quantity_holding = si.quantity_holding - A.total_hold
            WHERE si.quantity_holding > 0;

            UPDATE sales_orders so
            SET so.status = 'Cancelled' 
            WHERE status ='Pending' 
            AND date(so.created_at) = date(CURDATE());

            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS release_daily_holding_quantity');
    }
};
