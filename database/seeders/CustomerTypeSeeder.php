<?php

namespace Database\Seeders;

use App\Models\CustomerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
         DB::table('customer_types')->insert([

                ['name'=>'Individual'],
                ['name'=>'Business']
         ]);
    }
}
