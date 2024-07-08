<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('carriers')->insert([

        ['name'=>'Kwik Delivery'],
        ['name'=>'Gokada'],
        ['name'=>'GIG Logistics'],
        ['name'=>'DHL Delivery Company'],
        
        
        

      ]);
    }
}
