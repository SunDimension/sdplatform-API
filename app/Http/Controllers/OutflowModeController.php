<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutflowModeStoreRequest;
use App\Http\Requests\OutflowModeUpdateRequest;
use App\Http\Resources\OutflowModeCollection;
use App\Http\Resources\OutflowModeResource;
use App\Models\OutflowMode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutflowModeController extends Controller
{
    public function index(Request $request): OutflowModeCollection
    {
        $outflowmode = OutflowMode::all();

        return new OutflowModeCollection($outflowmode);
    }
    public function store(OutflowModeStoreRequest $request): OutflowModeResource
    {
        $outflowmode = OutflowMode::create($request->validated());

        return new OutflowModeResource($outflowmode);
    }

    public function show(Request $request, OutflowMode $outflowmode): OutflowModeResource
    {
        return new OutflowModeResource($outflowmode);
    }

    public function update(OutflowModeUpdateRequest $request, OutflowMode $outflowmode): OutflowModeResource
    {
        $outflowmode->update($request->validated());

        return new OutflowModeResource($outflowmode);
    }

   public function destroy($id)
    {   
       
        OutflowMode::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}



