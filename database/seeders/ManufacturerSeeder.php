<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('manufacturers')->insert([

        ['name'=>'BUA Foods'],
        ['name'=>'Golden Penny Foods'],
        ['name'=>'Flour Mills of Nigeria'],
        ['name'=>'Dangote Group'],
        ['name'=>'Flour Mills of Nigeria plc.'],
        ['name'=>'Nestle Nigeria plc.'],
        

      ]);
    }
}
