<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Foreign;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\JournalType;
use App\Models\User;

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
            'account_id' => Foreign::factory(),
            'account_no' => $this->faker->word(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
