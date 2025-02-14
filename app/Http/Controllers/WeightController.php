<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeightStoreRequest;
use App\Http\Requests\WeightUpdateRequest;
use App\Http\Resources\WeightCollection;
use App\Http\Resources\WeightResource;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeightController extends Controller
{
    public function index(Request $request): WeightCollection
    {
        $weights = Weight::all();

        return new WeightCollection($weights);
    }

    public function store(WeightStoreRequest $request): WeightResource
    {
        $weight = Weight::create($request->validated());

        return new WeightResource($weight);
    }

    public function show(Request $request, Weight $weight): WeightResource
    {
        return new WeightResource($weight);
    }

    public function update(WeightUpdateRequest $request, Weight $weight): WeightResource
    {
        $weight->update($request->validated());

        return new WeightResource($weight);
    }

  public function destroy($id)
    {   
       
        Weight::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
