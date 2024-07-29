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
    public function run()
    {
        
        DB::table('users')->insert([
            [
                'name' => 'App',
                'email' => 'admin@app.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'status_id' => 1,
                'branch_id' => 1,
                'warehouse_id' => 1,
                'created_at' => now()
            ]
        ]);

    }
}
