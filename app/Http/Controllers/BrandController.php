<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    public function index(Request $request): BrandCollection
    {
        $brands = Brand::all();

        return new BrandCollection($brands);
    }

    public function store(BrandStoreRequest $request): BrandResource
    {
        $brand = Brand::create($request->validated());

        return new BrandResource($brand);
    }

    public function show(Request $request, Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    public function update(BrandUpdateRequest $request, Brand $brand): BrandResource
    {
        $brand->update($request->validated());

        return new BrandResource($brand);
    }

    public function destroy(Request $request, Brand $brand): Response
    {
        $brand->delete();

        return response()->noContent();
    }
}
