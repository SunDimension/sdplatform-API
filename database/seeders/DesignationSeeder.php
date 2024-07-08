<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('designations')->insert([

        ['name'=>'Customer Service Manager'],
        ['name'=>'Human Resource Manager'],
        ['name'=>'Accountant'],
       
        

      ]);
    }
}
