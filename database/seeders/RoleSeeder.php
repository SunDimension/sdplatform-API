<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          DB::table('roles')->insert([

            ['name'=>'Admin'],
            ['name'=>'Sales Rep',],
            ['name'=>'Sales Supervisor'],
            ['name'=>'Cashier'],
            ['name'=>'Store Keeper'],
            ['name'=>'Store Manager'],
            ['name'=>'Branch Manager'],
            ['name'=>'Regional Manager'],
       ]);
    }
}
