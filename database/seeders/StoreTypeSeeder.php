<?php

namespace Database\Seeders;

use App\Models\StoreType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('store_types')->insert([

        ['name'=>'Mega Store'],
        ['name'=>'Mini Store']

      ]);
    }
}