<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Http\Resources\UnitCollection;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    public function index(Request $request): UnitCollection
    {
        $units = Unit::all();

        return new UnitCollection($units);
    }

    public function store(UnitStoreRequest $request): UnitResource
    {
        $unit = Unit::create($request->validated());

        return new UnitResource($unit);
    }

    public function show(Request $request, Unit $unit): UnitResource
    {
        return new UnitResource($unit);
    }

    public function update(UnitUpdateRequest $request, Unit $unit): UnitResource
    {
        $unit->update($request->validated());

        return new UnitResource($unit);
    }

   public function destroy($id)
    {   
       
        Unit::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
