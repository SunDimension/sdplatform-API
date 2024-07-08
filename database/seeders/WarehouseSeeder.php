<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warehouses')->insert([

        ['name'=>'Kano Warehouse', 'branch_id'=>3,'warehouse_address'=>'Sabon Geri Kano','zipcode'=>'522554','contact_person'=>'Hamza Mohammed','email'=>'ham@app.com','phone'=>'0851115555'],
        ['name'=>'Abuja Warehouse', 'branch_id'=>5,'warehouse_address'=>'Sabon Geri Kano','zipcode'=>'522554','contact_person'=>'Hamza Mohammed','email'=>'ham@app.com','phone'=>'0851115555'],
        
        

      ]);
    }
}
