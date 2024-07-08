<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('discounts')->insert([

            ['name'=>'1%'],
            ['name'=>'2.5%'],
            ['name'=>'5%'],
            ['name'=>'7.5%'],
            ['name'=>'10%']
       ]);
    }
}
