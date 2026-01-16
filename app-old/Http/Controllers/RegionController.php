<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Http\Resources\RegionCollection;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegionController extends Controller
{
    public function index(Request $request): RegionCollection
    {
        $regions = Region::all();

        return new RegionCollection($regions);
    }

    public function store(RegionStoreRequest $request): RegionResource
    {
        $regions = Region::create($request->validated());

        return new RegionResource($regions);
    }

    public function show(Request $request, Region $region): RegionResource
    {
        return new RegionResource($region);
    }

    public function update(RegionUpdateRequest $request, $regions): RegionResource
    {
        $regions->update($request->validated());

        return new RegionResource($regions);
    }

    public function destroy($id)
    {   
       
        Region::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
