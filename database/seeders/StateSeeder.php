<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            State::insert([
        ['name' => 'Abia'],
        ['name' => 'Adamawa'],
        ['name' => 'Akwa Ibom'],
        ['name'=>'Kano'],
       ]);
    }
}
