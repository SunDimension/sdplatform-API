<?php

namespace Database\Seeders;

use App\Models\SalesType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('sales_type')->insert([

        ['name'=>'Cash Sales'],
        ['name'=>'Credit Sales'],
        ['name'=>'Bank Sales']

      ]);
    }
}