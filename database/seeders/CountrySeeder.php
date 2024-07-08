<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
    
        DB::table('countries')->insert([

        ['name'=>'Nigeria'],
        ['name'=>'Ghana'],
        ['name'=>'Niger'],
        ['name'=>'Togo'],
        ['name'=>'Mali']
        
        

      ]);
    }
}
