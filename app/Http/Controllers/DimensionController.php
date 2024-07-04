<?php

namespace App\Http\Controllers;

use App\Http\Requests\DimensionStoreRequest;
use App\Http\Requests\DimensionUpdateRequest;
use App\Http\Resources\DimensionCollection;
use App\Http\Resources\DimensionResource;
use App\Models\Dimension;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DimensionController extends Controller
{
    public function index(Request $request): DimensionCollection
    {
        $dimensions = Dimension::all();

        return new DimensionCollection($dimensions);
    }

    public function store(DimensionStoreRequest $request): DimensionResource
    {
        $dimension = Dimension::create($request->validated());

        return new DimensionResource($dimension);
    }

    public function show(Request $request, Dimension $dimension): DimensionResource
    {
        return new DimensionResource($dimension);
    }

    public function update(DimensionUpdateRequest $request, Dimension $dimension): DimensionResource
    {
        $dimension->update($request->validated());

        return new DimensionResource($dimension);
    }

 public function destroy($id): Response
    {   
       
        Dimension::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
