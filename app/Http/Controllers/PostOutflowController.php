<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOutflowStoreRequest;
use App\Http\Requests\PostOutflowUpdateRequest;
use App\Http\Resources\PostOutflowCollection;
use App\Http\Resources\PostOutflowResource;
use App\Models\PostOutflow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostOutflowController extends Controller
{
    public function index(Request $request): PostOutflowCollection
    {
        $postoutflow = PostOutflow::all();

        return new PostOutflowCollection($postoutflow);
    }
    public function store(PostOutflowStoreRequest $request): PostOutflowResource
    {
        $postoutflow = PostOutflow::create($request->validated());

        return new PostOutflowResource($postoutflow);
    }

    public function show(Request $request, PostOutflow $postoutflow): PostOutflowResource
    {
        return new PostOutflowResource($postoutflow);
    }

    public function update(PostOutflowUpdateRequest $request, PostOutflow $postoutflow): PostOutflowResource
    {
        $postoutflow->update($request->validated());

        return new PostOutflowResource($postoutflow);
    }

   public function destroy($id)
    {   
       
        PostOutflow::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

