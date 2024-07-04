<?php

namespace Database\Seeders;

use App\Models\AccountSubtype;
use Illuminate\Database\Seeder;

class AccountSubtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountSubtype::factory()->count(5)->create();
    }
}
