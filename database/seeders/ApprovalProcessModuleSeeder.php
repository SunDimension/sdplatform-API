<?php

namespace Database\Seeders;

use App\Models\ApprovalProcessModule;
use Illuminate\Database\Seeder;

class ApprovalProcessModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalProcessModule::factory()->count(5)->create();
    }
}
