<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Country::insert([
        ['name' => 'Nigeria'],
        ['name' => 'USA'],
        ['name' => 'Ghana'],
        ['name'=>'Niger'],
       ]);
    }
}
