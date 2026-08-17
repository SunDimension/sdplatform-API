<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogPostsStoreRequest;
use App\Http\Requests\BlogPostsUpdateRequest;
use App\Http\Resources\BlogPostCollection;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogPostsController extends Controller
{
    public function index(Request $request): BlogPostCollection
    {
        $blogPosts = BlogPost::all();

        return new BlogPostCollection($blogPosts);
    }

    public function store(BlogPostsStoreRequest $request): BlogPostResource
    {
        $blogPost = BlogPost::create($request->validated());

        return new BlogPostResource($blogPost);
    }

    public function show(Request $request, BlogPost $blogPost): BlogPostResource
    {
        return new BlogPostResource($blogPost);
    }

    public function update(BlogPostsUpdateRequest $request, BlogPost $blogPost): BlogPostResource
    {
        $blogPost->update($request->validated());

        return new BlogPostResource($blogPost);
    }

    public function destroy(Request $request, BlogPost $blogPost): Response
    {
        $blogPost->delete();

        return response()->noContent();
    }
}
