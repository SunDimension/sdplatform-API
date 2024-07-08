<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder

{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor_types')->insert([

        ['name'=>'Access Bank'],
        ['name'=>'First Bank'],
        ['name'=>'GTB'],
        ['name'=>'Fidelity Bank'],
        ['name'=>'UBA Bank'],
        ['name'=>'Jaiz Bank'],
        ['name'=>'FCMB'],
        ['name'=>'Eco Bank'],
        ['name'=>'Moniepoint'],
        ['name'=>'Opay'],
       ]);
    }
}
