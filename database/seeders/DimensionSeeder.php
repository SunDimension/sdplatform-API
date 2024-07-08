<?php

namespace Database\Seeders;

use App\Models\Dimension;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class DimensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('dimensions')->insert([
            ['name'=>'cm'],
            ['name'=>'in']
         ]);
    }
}
