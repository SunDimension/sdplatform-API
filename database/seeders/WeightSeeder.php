<?php

namespace Database\Seeders;

use App\Models\Weight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class WeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('weights')->insert([

            ['name'=>'kg'],
            ['name'=>'g'],
            ['name'=>'ib']
         ]);
    }
}
