<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesTypeStoreRequest;
use App\Http\Requests\SalesTypeUpdateRequest;
use App\Http\Resources\SalesTypeCollection;
use App\Http\Resources\SalesTypeResource;
use App\Models\SalesType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesTypeController extends Controller
{
    public function index(Request $request): SalesTypeCollection
    {
        $salestype = SalesType::all();

        return new SalesTypeCollection($salestype);
    }
    public function store(SalesTypeStoreRequest $request): SalesTypeResource
    {
        $salestype = SalesType::create($request->validated());

        return new SalesTypeResource($salestype);
    }

    public function show(Request $request, SalesType $salestype): SalesTypeResource
    {
        return new SalesTypeResource($salestype);
    }

    public function update(SalesTypeUpdateRequest $request, SalesType $salestype): SalesTypeResource
    {
        $salestype->update($request->validated());

        return new SalesTypeResource($salestype);
    }

   public function destroy($id): Response
    {   
       
        SalesType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

