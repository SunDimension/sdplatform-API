<?php

namespace Database\Seeders;

use App\Models\AdjustmentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class AdjustmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('adjustment_types')->insert([

        ['name'=>'Quantity Adjustment'],
        ['name'=>'Value Adjustment']

       ]);
    }
}
