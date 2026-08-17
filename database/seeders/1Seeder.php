<?php

namespace Database\Seeders;

use App\Models\1;
use Illuminate\Database\Seeder;

class 1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        1::factory()->count(5)->create();
    }
}
