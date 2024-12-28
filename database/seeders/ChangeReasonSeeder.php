<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeReasonSeeder extends Seeder

{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('change_reasons')->insert([
        ['id'=> 1,'name'=>'Store Receipt'],
        ['id'=> 2,'name'=>'Vendor Price Change'],
        ['id'=> 3,'name'=>'Others'],
       ]);
    }
}
