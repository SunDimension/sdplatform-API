<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseStoreRequest;
use App\Http\Requests\ReleaseUpdateRequest;
use App\Http\Resources\ReleaseCollection;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReleaseController extends Controller
{
    
    public function index(Request $request): ReleaseCollection
    {
        $release = Release::all();

        return new ReleaseCollection($release);
    }
    public function store(ReleaseStoreRequest $request): ReleaseResource
    {
        $release = Release::create($request->validated());

        return new ReleaseResource($release);
    }

    public function show(Request $request, Release $release): ReleaseResource
    {
        return new ReleaseResource($release);
    }

    public function update(ReleaseUpdateRequest $request, Release $release): ReleaseResource
    {
        $release->update($request->validated());

        return new ReleaseResource($release);
    }

   public function destroy($id)
    {   
       
        Release::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
}

}