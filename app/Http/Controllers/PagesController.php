<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagesStoreRequest;
use App\Http\Requests\PagesUpdateRequest;
use App\Http\Resources\PageCollection;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PagesController extends Controller
{
    public function index(Request $request): PageCollection
    {
        $pages = Page::all();

        return new PageCollection($pages);
    }

    public function store(PagesStoreRequest $request): PageResource
    {
        $page = Page::create($request->validated());

        return new PageResource($page);
    }

    public function show(Request $request, Page $page): PageResource
    {
        return new PageResource($page);
    }

    public function update(PagesUpdateRequest $request, Page $page): PageResource
    {
        $page->update($request->validated());

        return new PageResource($page);
    }

    public function destroy(Request $request, Page $page): Response
    {
        $page->delete();

        return response()->noContent();
    }
}
