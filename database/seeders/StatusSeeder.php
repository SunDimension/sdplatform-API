<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
              Status::insert([
        ['name' => 'Active'],
        ['name' => 'Inactive'],
        ['name' => 'Out of Stock'],
        
       ]);
    }
}
