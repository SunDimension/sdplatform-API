<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreasStoreRequest;
use App\Http\Requests\AreasUpdateRequest;
use App\Http\Resources\AreaCollection;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AreasController extends Controller
{
    public function index(Request $request): AreaCollection
    {
        $areas = Area::all();

        return new AreaCollection($areas);
    }

    public function store(AreasStoreRequest $request): AreaResource
    {
        $area = Area::create($request->validated());

        return new AreaResource($area);
    }

    public function show(Request $request, Area $area): AreaResource
    {
        return new AreaResource($area);
    }

    public function update(AreasUpdateRequest $request, Area $area): AreaResource
    {
        $area->update($request->validated());

        return new AreaResource($area);
    }

    public function destroy(Request $request, Area $area): Response
    {
        $area->delete();

        return response()->noContent();
    }
}
