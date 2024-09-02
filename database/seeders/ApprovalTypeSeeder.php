<?php

namespace Database\Seeders;

use App\Models\ApprovalType;
use Illuminate\Database\Seeder;

class ApprovalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalType::factory()->count(5)->create();
    }
}
