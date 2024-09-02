<?php

namespace Database\Seeders;

use App\Models\ApprovalInstance;
use Illuminate\Database\Seeder;

class ApprovalInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApprovalInstance::factory()->count(5)->create();
    }
}
