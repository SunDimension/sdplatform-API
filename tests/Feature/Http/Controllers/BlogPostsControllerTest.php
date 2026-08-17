<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\BlogPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BlogPostsController
 */
final class BlogPostsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $blogPosts = BlogPosts::factory()->count(3)->create();

        $response = $this->get(route('blog-posts.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BlogPostsController::class,
            'store',
            \App\Http\Requests\BlogPostsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $title = $this->faker->sentence(4);
        $slug = $this->faker->slug();
        $content = $this->faker->paragraphs(3, true);
        $status = $this->faker->word();
        $author = Author::factory()->create();

        $response = $this->post(route('blog-posts.store'), [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
            'author_id' => $author->id,
        ]);

        $blogPosts = BlogPost::query()
            ->where('title', $title)
            ->where('slug', $slug)
            ->where('content', $content)
            ->where('status', $status)
            ->where('author_id', $author->id)
            ->get();
        $this->assertCount(1, $blogPosts);
        $blogPost = $blogPosts->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $blogPost = BlogPosts::factory()->create();

        $response = $this->get(route('blog-posts.show', $blogPost));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BlogPostsController::class,
            'update',
            \App\Http\Requests\BlogPostsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $blogPost = BlogPosts::factory()->create();
        $title = $this->faker->sentence(4);
        $slug = $this->faker->slug();
        $content = $this->faker->paragraphs(3, true);
        $status = $this->faker->word();
        $author = Author::factory()->create();

        $response = $this->put(route('blog-posts.update', $blogPost), [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
            'author_id' => $author->id,
        ]);

        $blogPost->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $blogPost->title);
        $this->assertEquals($slug, $blogPost->slug);
        $this->assertEquals($content, $blogPost->content);
        $this->assertEquals($status, $blogPost->status);
        $this->assertEquals($author->id, $blogPost->author_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $blogPost = BlogPosts::factory()->create();
        $blogPost = BlogPost::factory()->create();

        $response = $this->delete(route('blog-posts.destroy', $blogPost));

        $response->assertNoContent();

        $this->assertModelMissing($blogPost);
    }
}
