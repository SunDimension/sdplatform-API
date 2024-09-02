<?php

namespace Database\Seeders;

use App\Models\ApprovalProcessFlow;
use Illuminate\Database\Seeder;

class ApprovalProcessFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalProcessFlow::factory()->count(5)->create();
    }
}
