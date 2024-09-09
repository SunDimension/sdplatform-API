<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeStoreRequest;
use App\Http\Requests\StoreTypeUpdateRequest;
use App\Http\Resources\StoreTypeCollection;
use App\Http\Resources\StoreTypeResource;
use App\Models\StoreType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreTypeController extends Controller
{
    public function index(Request $request): StoreTypeCollection
    {
        $storetype = StoreType::all();

        return new StoreTypeCollection($storetype);
    }
    public function store(StoreTypeStoreRequest $request): StoreTypeResource
    {
        $storetype = StoreType::create($request->validated());

        return new StoreTypeResource($storetype);
    }

    public function show(Request $request, StoreType $storetype): StoreTypeResource
    {
        return new StoreTypeResource($storetype);
    }

    public function update(StoreTypeUpdateRequest $request, StoreType $storetype): StoreTypeResource
    {
        $storetype->update($request->validated());

        return new StoreTypeResource($storetype);
    }

   public function destroy($id)
    {   
       
        StoreType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}


