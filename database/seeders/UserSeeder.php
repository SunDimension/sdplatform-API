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
                'store_id' => 2,
                'status_id' => 1,
                'branch_id' => 2,
                'warehouse_id' => 6,
                'created_at' => now()
            ],

              [
                'name' => 'Umar Farok',
                'email' => 'farouk@hamirglobal.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'store_id' => 2,
                'status_id' => 1,
                'branch_id' => 2,
                'warehouse_id' => 6,
                'created_at' => now()
            ],
                 [
                'name' => 'Yusuf',
                'email' => 'yusuf@hamirglobal.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'store_id' => 2,
                'status_id' => 1,
                'branch_id' => 2,
                'warehouse_id' => 6,
                'created_at' => now()
            ],

                 [
                'name' => 'Admin',
                'email' => 'admin@hamirglobal.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'store_id' => 2,
                'status_id' => 1,
                'branch_id' => 2,
                'warehouse_id' => 6,
                'created_at' => now()
            ]
        ]);

    }
}
