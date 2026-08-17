<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Page;
use App\Models\Pages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PagesController
 */
final class PagesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $pages = Pages::factory()->count(3)->create();

        $response = $this->get(route('pages.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagesController::class,
            'store',
            \App\Http\Requests\PagesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $title = $this->faker->sentence(4);
        $slug = $this->faker->slug();
        $content = $this->faker->paragraphs(3, true);
        $status = $this->faker->word();

        $response = $this->post(route('pages.store'), [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
        ]);

        $pages = Page::query()
            ->where('title', $title)
            ->where('slug', $slug)
            ->where('content', $content)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $pages);
        $page = $pages->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $page = Pages::factory()->create();

        $response = $this->get(route('pages.show', $page));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagesController::class,
            'update',
            \App\Http\Requests\PagesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $page = Pages::factory()->create();
        $title = $this->faker->sentence(4);
        $slug = $this->faker->slug();
        $content = $this->faker->paragraphs(3, true);
        $status = $this->faker->word();

        $response = $this->put(route('pages.update', $page), [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
        ]);

        $page->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $page->title);
        $this->assertEquals($slug, $page->slug);
        $this->assertEquals($content, $page->content);
        $this->assertEquals($status, $page->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $page = Pages::factory()->create();
        $page = Page::factory()->create();

        $response = $this->delete(route('pages.destroy', $page));

        $response->assertNoContent();

        $this->assertModelMissing($page);
    }
}
