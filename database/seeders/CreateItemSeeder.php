<?php

namespace Database\Seeders;

use App\Models\CreateItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('create_items')->insert([

            ['name'=>'Viva Plus 180G','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>3,'vendor_id'=>1,'store_id'=>1,'user_id'=>6],

           ['name'=>'Delfin White Soap x 24','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'','unit_id'=>5,'brand_id'=>2,'cost_price'=>'4500','selling_price'=>'5600','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>4,'vendor_id'=>1,'store_id'=>1,'user_id'=>6],

           ['name'=>'Chic Soap','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>4,'vendor_id'=>1,'store_id'=>2,'user_id'=>6],

         ['name'=>'Dangote Flour','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>4,'vendor_id'=>1,'store_id'=>2,'user_id'=>6],

          ['name'=>'Kings 5Ltrs x 4','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>3,'vendor_id'=>1,'store_id'=>2,'user_id'=>6],

          ['name'=>'Nittol Antiodour 90g x 51','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>4,'vendor_id'=>1,'store_id'=>1,'user_id'=>6],
           
            ['name'=>'Goldenvita 1kg','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>3,'vendor_id'=>1,'store_id'=>1,'user_id'=>6],


 ['name'=>'Viva Plus 350G','item_category_id'=>5,'item_type_id'=>2,'description'=>'Detergent','batch_number'=>'5545554','unit_id'=>5,'brand_id'=>2,'cost_price'=>'5000','selling_price'=>'','reorder_level'=>'','dimension_id'=>2,'weight_id'=>3,'branch_id'=>3,'warehouse'=>4,'vendor_id'=>1,'store_id'=>2,'user_id'=>6],

          
        ]);
    }
}
