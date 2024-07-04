<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Employees,id;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\JournalType;

class JournalEntryDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalEntryDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'journal_type_id' => JournalType::factory(),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'description' => $this->faker->text(),
            'account_id' => $this->faker->word(),
            'account_no' => $this->faker->word(),
            'created_by' => $this->faker->word(),
            'modified_by' => $this->faker->word(),
            'deleted_by' => Employees,id::factory()->create()->deleted_by,
        ];
    }
}
