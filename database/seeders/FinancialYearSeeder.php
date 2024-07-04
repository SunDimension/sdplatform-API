<?php

namespace Database\Seeders;

use App\Models\FinancialYear;
use Illuminate\Database\Seeder;

class FinancialYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialYear::factory()->count(5)->create();
    }
}
