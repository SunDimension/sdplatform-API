<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use Illuminate\Database\Seeder;

class AccountGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountGroup::create([
            'name' => 'Balance Sheet',
            // add other necessary fields here
        ]);
        AccountGroup::create([
            'name' => 'Profit and Loss',
            // add other necessary fields here
        ]);
    
    }
}
