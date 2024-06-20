<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Branch::insert([
        ['branch_name' => 'Kano','branch_address'=>'Sabon Gare','contact_person'=>'james','branch_email'=>'kano-main@go.com','branch_phone'=>'08755212525','state_id'=>'4','country_id'=>'1'],
        ]
        
        );
    }
}
