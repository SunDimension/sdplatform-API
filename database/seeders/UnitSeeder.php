<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([

            ['name'=>'cm'],
             ['name'=>'ft'],
             ['name'=>'g'],
             ['name'=>'kg'],
             ['name'=>'mg'],
             ['name'=>'m'],
             ['name'=>'Ib'],
             ['name'=>'in'],
             ['name'=>'pcs'],
        
        ]);
    }
}
