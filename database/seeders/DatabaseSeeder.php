<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            BankSeeder::class,
            RoleSeeder::class,
            StatusSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            BranchSeeder::class,
            StoreSeeder::class,

            UserSeeder::class,
            StoreItemSeeder::class,
            ChangeReasonSeeder::class,
            FinancialYearSeeder::class,
            FinancialQuarterSeeder::class,
            FinancialPeriodSeeder::class,
            //ChartProviderSeeder::class,
            //ChartCategorySeeder::class,
            //ChartTypeSeeder::class,
            //ChartSeeder::class,
            //DashboardSettingSeeder::class
        ]);
    }
}
