<?php

namespace Database\Seeders;

use App\Models\ApprovalLimit;
use Illuminate\Database\Seeder;

class ApprovalLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalLimit::factory()->count(5)->create();
    }
}
