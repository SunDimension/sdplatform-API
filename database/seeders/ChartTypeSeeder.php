<?php

namespace Database\Seeders;

use App\Models\ChartType;
use Illuminate\Database\Seeder;

class ChartTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChartType::factory()->count(5)->create();
    }
}
