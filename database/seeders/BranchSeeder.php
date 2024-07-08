<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branches')->insert([
        
            ['name'=>'Head Office Branch','address'=>'Kano Branch','contact_person'=>'Adamu','email'=>'adama@app.com','phone'=>'0826554655','state_id'=>23,'country_id'=>1],

             ['name'=>'Maraaba Branch','address'=>'Kano Branch','contact_person'=>'Kabiru','email'=>'adama@app.com','phone'=>'0826554655','state_id'=>9,'country_id'=>1],

             
             ['name'=>'Mabushi Branch','address'=>'Abuja Branch','contact_person'=>'Kabiru','email'=>'adama@app.com','phone'=>'0826554655','state_id'=>9,'country_id'=>1],
            
        ]);
    }
}
