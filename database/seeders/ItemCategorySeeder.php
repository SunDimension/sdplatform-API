<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('item_categories')->insert([

            ['name'=>'Dairy Product'],
            ['name'=>'Drink'],
            ['name'=>'Condiment'],
            ['name'=>'Personal care'],
            ['name'=>'Beverages snacks'],
            ['name'=>'Pasta'],
        ]);
    }
}
