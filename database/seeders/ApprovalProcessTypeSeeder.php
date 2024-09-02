<?php

namespace Database\Seeders;

use App\Models\ApprovalProcessType;
use Illuminate\Database\Seeder;

class ApprovalProcessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalProcessType::factory()->count(5)->create();
    }
}
