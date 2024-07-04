<?php

namespace Database\Seeders;

use App\Models\ChartCard;
use Illuminate\Database\Seeder;

class ChartCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChartCard::factory()->count(5)->create();
    }
}
