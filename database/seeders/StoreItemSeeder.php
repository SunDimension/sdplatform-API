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

            ['item_category_id'=>1, 'create_item_id'=>107,'unit_id'=>4,'cost_price'=>'785.00','selling_price'=>'1500.00','reorder_level'=>'500','store_id'=>1,'quantity'=>150,'discount'=>200],

         ]);
    }
}
