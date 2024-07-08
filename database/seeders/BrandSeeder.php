<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('brands')->insert([

            ['name'=>'Dangote Industries Limited'],
            ['name'=>'Cadbury Nigeria Plc'],
            ['name'=>'Nestle Nigeria'],
            ['name'=>'Ayoola Foods'],
            ['name'=>'Crown Flour Mill Limited'],
            ['name'=>'DUFIL Prima Foods Plc'],
            ['name'=>'UAC Foods Limited'],
       ]);
    }
}
