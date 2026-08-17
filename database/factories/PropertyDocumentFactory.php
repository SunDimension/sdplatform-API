<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyDocument;

class PropertyDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyDocument::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'survey_plan_url' => $this->faker->word(),
            'title' => $this->faker->sentence(4),
            'CofO' => $this->faker->word(),
            'floor_plan' => $this->faker->word(),
            'approval_letter' => $this->faker->word(),
            'document_type' => $this->faker->word(),
            'verified' => $this->faker->boolean(),
            'document_url' => $this->faker->word(),
        ];
    }
}
