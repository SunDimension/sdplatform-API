<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChartCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChartCardController
 */
final class ChartCardControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $chartCards = ChartCard::factory()->count(3)->create();

        $response = $this->get(route('chart-cards.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartCardController::class,
            'store',
            \App\Http\Requests\ChartCardStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $card_title = $this->faker->word();
        $card_size = $this->faker->word();
        $is_active = $this->faker->word();
        $sql_query = $this->faker->text();
        $module_id = $this->faker->word();
        $submodule_id = $this->faker->word();
        $sequence = $this->faker->word();
        $color = $this->faker->word();

        $response = $this->post(route('chart-cards.store'), [
            'card_title' => $card_title,
            'card_size' => $card_size,
            'is_active' => $is_active,
            'sql_query' => $sql_query,
            'module_id' => $module_id,
            'submodule_id' => $submodule_id,
            'sequence' => $sequence,
            'color' => $color,
        ]);

        $chartCards = ChartCard::query()
            ->where('card_title', $card_title)
            ->where('card_size', $card_size)
            ->where('is_active', $is_active)
            ->where('sql_query', $sql_query)
            ->where('module_id', $module_id)
            ->where('submodule_id', $submodule_id)
            ->where('sequence', $sequence)
            ->where('color', $color)
            ->get();
        $this->assertCount(1, $chartCards);
        $chartCard = $chartCards->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $chartCard = ChartCard::factory()->create();

        $response = $this->get(route('chart-cards.show', $chartCard));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ChartCardController::class,
            'update',
            \App\Http\Requests\ChartCardUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $chartCard = ChartCard::factory()->create();
        $card_title = $this->faker->word();
        $card_size = $this->faker->word();
        $is_active = $this->faker->word();
        $sql_query = $this->faker->text();
        $module_id = $this->faker->word();
        $submodule_id = $this->faker->word();
        $sequence = $this->faker->word();
        $color = $this->faker->word();

        $response = $this->put(route('chart-cards.update', $chartCard), [
            'card_title' => $card_title,
            'card_size' => $card_size,
            'is_active' => $is_active,
            'sql_query' => $sql_query,
            'module_id' => $module_id,
            'submodule_id' => $submodule_id,
            'sequence' => $sequence,
            'color' => $color,
        ]);

        $chartCard->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($card_title, $chartCard->card_title);
        $this->assertEquals($card_size, $chartCard->card_size);
        $this->assertEquals($is_active, $chartCard->is_active);
        $this->assertEquals($sql_query, $chartCard->sql_query);
        $this->assertEquals($module_id, $chartCard->module_id);
        $this->assertEquals($submodule_id, $chartCard->submodule_id);
        $this->assertEquals($sequence, $chartCard->sequence);
        $this->assertEquals($color, $chartCard->color);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $chartCard = ChartCard::factory()->create();

        $response = $this->delete(route('chart-cards.destroy', $chartCard));

        $response->assertNoContent();

        $this->assertSoftDeleted($chartCard);
    }
}
