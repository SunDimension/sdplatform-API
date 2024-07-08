<?php

namespace Database\Seeders;

use App\Models\VendorType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor_types')->insert([

        ['name'=>'Service provider'],
        ['name'=>'Manufacturer'],
        ['name'=>'Retailer'],
        ['name'=>'Distributors'],
       
        

      ]);
    }
}
