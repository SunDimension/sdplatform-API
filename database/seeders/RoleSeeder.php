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

            ['id'=>1,'name'=>'Admin'],
            ['id'=>2,'name'=>'Sales Rep'],
            ['id'=>3,'name'=>'Sales Supervisor'],
            ['id'=>4,'name'=>'Cashier'],
            ['id'=>5,'name'=>'Store Keeper'],
            ['id'=>6,'name'=>'Store Manager'],
            ['id'=>7,'name'=>'Branch Manager'],
            ['id'=>8,'name'=>'Regional Manager'],
       ]);
    }
}
