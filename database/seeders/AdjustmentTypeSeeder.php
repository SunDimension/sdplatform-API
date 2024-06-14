<?php

namespace Database\Seeders;

use App\Models\AdjustmentType;
use Illuminate\Database\Seeder;

class AdjustmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdjustmentType::factory()->count(5)->create();
    }
}
