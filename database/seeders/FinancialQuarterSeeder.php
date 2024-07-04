<?php

namespace Database\Seeders;

use App\Models\FinancialQuarter;
use Illuminate\Database\Seeder;

class FinancialQuarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialQuarter::factory()->count(5)->create();
    }
}
