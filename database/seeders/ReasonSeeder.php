<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('reasons')->insert([

            ['name'=>'Stolen goods'],
            ['name'=>'Damaged goods'],
            ['name'=>'Stock Written off'],
            ['name'=>'Stocktaking results'],
            ['name'=>'Inventory Revaluation'],
            
       ]);
    }
}
