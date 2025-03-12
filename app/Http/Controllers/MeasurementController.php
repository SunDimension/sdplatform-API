<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeasurementStoreRequest;
use App\Http\Requests\MeasurementUpdateRequest;
use App\Http\Resources\MeasurementCollection;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeasurementController extends Controller
{
    public function index(Request $request): MeasurementCollection
    {
        $measurement = Measurement::all();

        return new MeasurementCollection($measurement);
    }
    public function store(MeasurementStoreRequest $request): MeasurementResource
    {
        $measurement = Measurement::create($request->validated());

        return new MeasurementResource($measurement);
    }

    public function show(Request $request, Measurement $measurement): MeasurementResource
    {
        return new MeasurementResource($measurement);
    }

    public function update(MeasurementUpdateRequest $request, Measurement $measurement): MeasurementResource
    {
        $measurement->update($request->validated());

        return new MeasurementResource($measurement);
    }

   public function destroy($id)
    {   
       
        Measurement::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

