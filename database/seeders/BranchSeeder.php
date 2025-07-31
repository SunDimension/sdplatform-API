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
        
            [
                'name' => 'Head Office Branch',
                'address' => 'Kano Branch',
                'email' => 'adama@app.com',
                'phone' => '0826554655',
                'is_active' => true,
                'created_at' => now()
            ],

            [
                'name' => 'Maraaba Branch',
                'address' => 'Kano Branch',
                'email' => 'adama@app.com',
                'phone' => '0826554655',
                'is_active' => true,
                'created_at' => now()
            ],

            [
                'name' => 'Mabushi Branch',
                'address' => 'Abuja Branch',
                'email' => 'adama@app.com',
                'phone' => '0826554655',
                'is_active' => true,
                'created_at' => now()
            ],
            
        ]);
    }
}
