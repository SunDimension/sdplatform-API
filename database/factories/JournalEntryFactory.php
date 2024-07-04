<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Employees,id;
use App\Models\JournalEntry;
use App\Models\Warehouse;

class JournalEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(),
            'payment_date' => $this->faker->dateTime(),
            'warehouse_id' => Warehouse::factory(),
            'vendor_id' => $this->faker->word(),
            'created_by' => $this->faker->word(),
            'modified_by' => $this->faker->word(),
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
