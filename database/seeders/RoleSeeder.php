<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
        ['name' => 'Super Admin'],
        ['name' => 'Accountant'],
        ['name' => 'Inventory'],
        ['name'=>'cashier'],
        ['name'=>'Chief Cashier'],
        ['name'=>'Sales'],
        ]);
        
        
    }
}
