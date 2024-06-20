<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          Warehouse::insert([
        ['warehouse_name' => 'Kano-Main','branch_id'=>'1','warehouse_address'=>'wuye','zipcode'=>'125452','contact_person'=>'Emeka','email'=>'kay@gap.com','phone'=>'087565552365'],
       
        ]);
    }
}
