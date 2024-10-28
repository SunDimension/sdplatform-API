<?php

namespace Database\Seeders;

use App\Models\CreateItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('store_items')->insert([

            // ['item_category_id'=>1, 'create_item_id'=>107,'unit_id'=>4,'cost_price'=>'785.00','selling_price'=>'1500.00','reorder_level'=>'500','store_id'=>1,'quantity'=>150,'discount'=>200, 'user_id'=>6 ],

             ['item_category_id'=>1, 'create_item_id'=>108,'unit_id'=>5,'cost_price'=>'5000.00','selling_price'=>'6000.00','reorder_level'=>'650','store_id'=>2,'quantity'=>500,'discount'=>200, 'user_id'=>6 ],

             
             ['item_category_id'=>1, 'create_item_id'=>109,'unit_id'=>5,'cost_price'=>'4000.00','selling_price'=>'4600.00','reorder_level'=>'600','store_id'=>1,'quantity'=>369,'discount'=>200, 'user_id'=>6 ],

               ['item_category_id'=>6, 'create_item_id'=>110,'unit_id'=>4,'cost_price'=>'6000.00','selling_price'=>'8499.00','reorder_level'=>'600','store_id'=>1,'quantity'=>700,'discount'=>200, 'user_id'=>6 ],

               

            //    ['item_category_id'=>3, 'create_item_id'=>111,'unit_id'=>4,'cost_price'=>'2000.00','selling_price'=>'3500.00','reorder_level'=>'600','store_id'=>1,'quantity'=>700,'discount'=>200, 'user_id'=>6 ],

                // ['item_category_id'=>6, 'create_item_id'=>112,'unit_id'=>4,'cost_price'=>'17000.00','selling_price'=>'17500.00','reorder_level'=>'600','store_id'=>2,'quantity'=>34,'discount'=>200, 'user_id'=>6 ],

         ]);
    }
}
