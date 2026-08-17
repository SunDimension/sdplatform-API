<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyImagesStoreRequest;
use App\Http\Requests\PropertyImagesUpdateRequest;
use App\Http\Resources\PropertyImageCollection;
use App\Http\Resources\PropertyImageResource;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyImagesController extends Controller
{
    public function index(Request $request): PropertyImageCollection
    {
        $propertyImages = PropertyImage::all();

        return new PropertyImageCollection($propertyImages);
    }

    public function store(PropertyImagesStoreRequest $request): PropertyImageResource
    {
        $propertyImage = PropertyImage::create($request->validated());

        return new PropertyImageResource($propertyImage);
    }

    public function show(Request $request, PropertyImage $propertyImage): PropertyImageResource
    {
        return new PropertyImageResource($propertyImage);
    }

    public function update(PropertyImagesUpdateRequest $request, PropertyImage $propertyImage): PropertyImageResource
    {
        $propertyImage->update($request->validated());

        return new PropertyImageResource($propertyImage);
    }

    public function destroy(Request $request, PropertyImage $propertyImage): Response
    {
        $propertyImage->delete();

        return response()->noContent();
    }
}
