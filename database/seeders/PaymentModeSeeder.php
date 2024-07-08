<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
    DB::table('payment_modes')->insert([

        ['name'=>'Cash'],
        ['name'=>'Bank Tranfer'],
        ['name'=>'Bank Remittance'],
        ['name'=>'Credit'],
        ['name'=>'Deposit']

    ]);
    }
}
