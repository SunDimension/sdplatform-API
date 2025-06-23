<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('taxes')->insert([
            [
                'name' => 'Standard VAT',
                'rate' => 7.50,
                'type' => 'vat',
                'is_active' => true,
                'description' => 'Standard Value Added Tax at 16%'
            ],
            [
                'name' => 'Zero Rated VAT',
                'rate' => 0.00,
                'type' => 'vat',
                'is_active' => true,
                'description' => 'Zero Rated Value Added Tax'
            ],
            [
                'name' => 'Exempt VAT',
                'rate' => 0.00,
                'type' => 'vat',
                'is_active' => true,
                'description' => 'Exempt from Value Added Tax'
            ],
            [
              'name' => 'Standard WHT - 10',
              'rate' => 10.00,
              'type' => 'wht',
              'is_active' => true,
              'description' => 'Standard Withholding Tax at 5%'
          ],
            [
                'name' => 'Standard WHT',
                'rate' => 5.00,
                'type' => 'wht',
                'is_active' => true,
                'description' => 'Standard Withholding Tax at 5%'
            ],
            [
                'name' => 'No Tax',
                'rate' => 0.00,
                'type' => 'wht',
                'is_active' => true,
                'description' => 'No tax applicable'
            ]
        ]);
    }
}
