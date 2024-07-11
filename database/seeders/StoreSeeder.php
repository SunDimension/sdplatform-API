<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stores')->insert([

        ['name'=>'Maraba Store', 'branch_id'=>3,'store_type_id'=>1],
        ['name'=>'Mabushi Store', 'branch_id'=>3,'store_type_id'=>1],
        
        
        

      ]);
    }
}
