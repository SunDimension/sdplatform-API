<?php

namespace Database\Seeders;

use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use App\Models\FinancialQuarter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FinancialPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default user for created_by
        $defaultUser = User::first() ?? User::factory()->create();

        // Create financial years for the last 5 years
        $financialYears = [];
        for ($year = 2023; $year <= 2027; $year++) {
            $financialYears[] = FinancialYear::create([
                'name' => "FY {$year}-" . ($year + 1),
                'date_from' => Carbon::create($year, 4, 1), // April 1st
                'date_to' => Carbon::create($year + 1, 3, 31), // March 31st next year
                'is_active' => $year === 2025, // Only current year is active
                'created_by' => $defaultUser->id,
                'modified_by' => $defaultUser->id,
            ]);
        }

        // Create financial quarters for each year
        $financialQuarters = [];
        foreach ($financialYears as $financialYear) {
            $yearStart = Carbon::parse($financialYear->date_from);
            
            for ($quarter = 1; $quarter <= 4; $quarter++) {
                $quarterStart = $yearStart->copy()->addMonths(($quarter - 1) * 3);
                $quarterEnd = $quarterStart->copy()->addMonths(3)->subDay();
                
                $financialQuarters[] = FinancialQuarter::create([
                    'name' => "Q{$quarter} FY {$financialYear->name}",
                    'date_from' => $quarterStart,
                    'date_to' => $quarterEnd,
                    'is_active' => $financialYear->is_active,
                    'financial_year_id' => $financialYear->id,
                    'created_by' => $defaultUser->id,
                    'modified_by' => $defaultUser->id,
                ]);
            }
        }

        // Create financial periods (months) for each quarter
        $periodsCreated = 0;
        foreach ($financialQuarters as $quarter) {
            $quarterStart = Carbon::parse($quarter->date_from);
            
            for ($month = 0; $month < 3; $month++) {
                if ($periodsCreated >= 12*5) break; // Stop after creating 20 periods
                
                $periodStart = $quarterStart->copy()->addMonths($month);
                $periodEnd = $periodStart->copy()->endOfMonth();
                
                FinancialPeriod::create([
                    'name' => $periodStart->format('M Y'),
                    'date_from' => $periodStart,
                    'date_to' => $periodEnd,
                    'is_active' => $quarter->is_active && $periodStart->isCurrentMonth(),
                    'financial_year_id' => $quarter->financial_year_id,
                    'financial_quarter_id' => $quarter->id,
                    'created_by' => $defaultUser->id,
                    'modified_by' => $defaultUser->id,
                ]);
                
                $periodsCreated++;
            }
        }

        // If we haven't created 20 periods yet, create additional ones
        if ($periodsCreated < 12*5) {
            $remainingPeriods = 12*5 - $periodsCreated;
            
            for ($i = 0; $i < $remainingPeriods; $i++) {
                $randomYear = $financialYears[array_rand($financialYears)];
                $randomQuarter = $randomYear->financialQuarters->random();
                
                $randomMonth = rand(0, 11);
                $periodStart = Carbon::parse($randomYear->date_from)->addMonths($randomMonth);
                $periodEnd = $periodStart->copy()->endOfMonth();
                
                FinancialPeriod::create([
                    'name' => $periodStart->format('M Y'),
                    'date_from' => $periodStart,
                    'date_to' => $periodEnd,
                    'is_active' => false,
                    'financial_year_id' => $randomYear->id,
                    'financial_quarter_id' => $randomQuarter->id,
                    'created_by' => $defaultUser->id,
                    'modified_by' => $defaultUser->id,
                ]);
            }
        }
    }
}
