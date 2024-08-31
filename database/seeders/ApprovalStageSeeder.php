<?php

namespace Database\Seeders;

use App\Models\ApprovalStage;
use Illuminate\Database\Seeder;

class ApprovalStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalStage::factory()->count(5)->create();
    }
}
