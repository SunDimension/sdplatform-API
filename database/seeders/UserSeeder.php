<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          DB::table('users')->insert([

            ['role_id'=>2,'name'=>'Admin','email'=>'admin@app.com','password'=>'password','status_id'=>1,'branch_id'=>1,'warehouse'=>1],
            
       ]);
    }
}
