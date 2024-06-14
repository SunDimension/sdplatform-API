<?php

namespace Database\Seeders;

use App\Models\CreateItem;
use Illuminate\Database\Seeder;

class CreateItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CreateItem::factory()->count(5)->create();
    }
}
