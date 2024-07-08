<?php

namespace Database\Seeders;

use App\Models\PaymentTerm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
         DB::table('payment_terms')->insert([

            ['name'=>'Immediately'],
            ['name'=>'Due on expected release date'],
            ['name'=>'Due end of the week'],
            ['name'=>'Due end of the month']
         ]);
    }
}
