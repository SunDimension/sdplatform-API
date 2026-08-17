<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FAQ;
use App\Models\FAQs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FAQsController
 */
final class FAQsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $fAQs = FAQs::factory()->count(3)->create();

        $response = $this->get(route('f-a-qs.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FAQsController::class,
            'store',
            \App\Http\Requests\FAQsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $question = $this->faker->word();
        $answer = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->post(route('f-a-qs.store'), [
            'question' => $question,
            'answer' => $answer,
            'status' => $status,
        ]);

        $fAQs = FAQ::query()
            ->where('question', $question)
            ->where('answer', $answer)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $fAQs);
        $fAQ = $fAQs->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $fAQ = FAQs::factory()->create();

        $response = $this->get(route('f-a-qs.show', $fAQ));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FAQsController::class,
            'update',
            \App\Http\Requests\FAQsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $fAQ = FAQs::factory()->create();
        $question = $this->faker->word();
        $answer = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->put(route('f-a-qs.update', $fAQ), [
            'question' => $question,
            'answer' => $answer,
            'status' => $status,
        ]);

        $fAQ->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($question, $fAQ->question);
        $this->assertEquals($answer, $fAQ->answer);
        $this->assertEquals($status, $fAQ->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $fAQ = FAQs::factory()->create();
        $fAQ = FAQ::factory()->create();

        $response = $this->delete(route('f-a-qs.destroy', $fAQ));

        $response->assertNoContent();

        $this->assertModelMissing($fAQ);
    }
}
