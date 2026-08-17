<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyVideosStoreRequest;
use App\Http\Requests\PropertyVideosUpdateRequest;
use App\Http\Resources\PropertyVideoCollection;
use App\Http\Resources\PropertyVideoResource;
use App\Models\PropertyVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyVideosController extends Controller
{
    public function index(Request $request): PropertyVideoCollection
    {
        $propertyVideos = PropertyVideo::all();

        return new PropertyVideoCollection($propertyVideos);
    }

    public function store(PropertyVideosStoreRequest $request): PropertyVideoResource
    {
        $propertyVideo = PropertyVideo::create($request->validated());

        return new PropertyVideoResource($propertyVideo);
    }

    public function show(Request $request, PropertyVideo $propertyVideo): PropertyVideoResource
    {
        return new PropertyVideoResource($propertyVideo);
    }

    public function update(PropertyVideosUpdateRequest $request, PropertyVideo $propertyVideo): PropertyVideoResource
    {
        $propertyVideo->update($request->validated());

        return new PropertyVideoResource($propertyVideo);
    }

    public function destroy(Request $request, PropertyVideo $propertyVideo): Response
    {
        $propertyVideo->delete();

        return response()->noContent();
    }
}
