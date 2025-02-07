<?php

namespace App\Http\Controllers;

use App\Http\Requests\InflowStatusStoreRequest;
use App\Http\Requests\InflowStatusUpdateRequest;
use App\Http\Resources\InflowStatusCollection;
use App\Http\Resources\InflowStatusResource;
use App\Models\InflowStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InflowStatusController extends Controller
{
    public function index(Request $request): InflowStatusCollection
    {
        $inflowstatus = InflowStatus::all();

        return new InflowStatusCollection($inflowstatus);
    }
    public function store(InflowStatusStoreRequest $request): InflowStatusResource
    {
        $inflowstatus = InflowStatus::create($request->validated());

        return new InflowStatusResource($inflowstatus);
    }

    public function show(Request $request, InflowStatus $inflowstatus): InflowStatusResource
    {
        return new InflowStatusResource($inflowstatus);
    }

    public function update(InflowStatusUpdateRequest $request, InflowStatus $inflowstatus): InflowStatusResource
    {
        $inflowstatus->update($request->validated());

        return new InflowStatusResource($inflowstatus);
    }

   public function destroy($id)
    {   
       
        InflowStatus::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}




