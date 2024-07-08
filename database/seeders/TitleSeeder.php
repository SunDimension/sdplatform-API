<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('titles')->insert([

        ['name'=>'Mr.'],
        ['name'=>'Mrs.'],
        ['name'=>'Alhaji'],
        ['name'=>'Hajiya'],
        

      ]);
    }
}
