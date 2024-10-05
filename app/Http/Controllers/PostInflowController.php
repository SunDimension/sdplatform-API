<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostInflowStoreRequest;
use App\Http\Requests\PostInflowUpdateRequest;
use App\Http\Resources\PostInflowCollection;
use App\Http\Resources\PostInflowResource;
use App\Models\PostInflow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostInflowController extends Controller
{
    public function index(Request $request): PostInflowCollection
    {
        $postinflow = PostInflow::all();

        return new PostInflowCollection($postinflow);
    }
    public function store(PostInflowStoreRequest $request): PostInflowResource
    {
        $postinflow = PostInflow::create($request->validated());

        return new PostInflowResource($postinflow);
    }

    public function show(Request $request, PostInflow $postinflow): PostInflowResource
    {
        return new PostInflowResource($postinflow);
    }

    public function update(PostInflowUpdateRequest $request, PostInflow $postinflow): PostInflowResource
    {
        $postinflow->update($request->validated());

        return new PostInflowResource($postinflow);
    }

   public function destroy($id)
    {   
       
        PostInflow::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}


