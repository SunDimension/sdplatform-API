<?php

namespace Database\Seeders;

use App\Models\ChartCategory;
use Illuminate\Database\Seeder;

class ChartCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChartCategory::factory()->count(5)->create();
    }
}
