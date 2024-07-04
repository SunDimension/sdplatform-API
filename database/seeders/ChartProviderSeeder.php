<?php

namespace Database\Seeders;

use App\Models\ChartProvider;
use Illuminate\Database\Seeder;

class ChartProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChartProvider::factory()->count(5)->create();
    }
}
