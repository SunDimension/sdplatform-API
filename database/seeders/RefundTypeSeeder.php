<?php

namespace Database\Seeders;

use App\Models\RefundType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefundTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('refund_types')->insert([

        ['name'=>'Cash Refund'],
        ['name'=>'Bank Transfer'],
       

      ]);
    }
}