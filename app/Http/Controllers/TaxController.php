<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxStoreRequest;
use App\Http\Requests\TaxUpdateRequest;
use App\Http\Resources\TaxCollection;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaxController extends Controller
{
    public function index(Request $request): TaxCollection
    {
        $taxes = Tax::all();

        return new TaxCollection($taxes);
    }

    public function store(TaxStoreRequest $request): TaxResource
    {
        $tax = Tax::create($request->validated());

        return new TaxResource($tax);
    }

    public function show(Request $request, Tax $tax): TaxResource
    {
        return new TaxResource($tax);
    }

    public function update(TaxUpdateRequest $request, Tax $tax): TaxResource
    {
        $tax->update($request->validated());

        return new TaxResource($tax);
    }

    public function destroy($id)
    {   
       
        Tax::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
