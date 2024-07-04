<?php

namespace Database\Seeders;

use App\Models\FinancialPeriod;
use Illuminate\Database\Seeder;

class FinancialPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialPeriod::factory()->count(5)->create();
    }
}
