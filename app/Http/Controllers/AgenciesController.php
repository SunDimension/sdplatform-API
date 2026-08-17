<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgenciesStoreRequest;
use App\Http\Requests\AgenciesUpdateRequest;
use App\Http\Resources\AgencyCollection;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AgenciesController extends Controller
{
    public function index(Request $request): AgencyCollection
    {
        $agencies = Agency::all();

        return new AgencyCollection($agencies);
    }

    public function store(AgenciesStoreRequest $request): AgencyResource
    {
        $agency = Agency::create($request->validated());

        return new AgencyResource($agency);
    }

    public function show(Request $request, Agency $agency): AgencyResource
    {
        return new AgencyResource($agency);
    }

    public function update(AgenciesUpdateRequest $request, Agency $agency): AgencyResource
    {
        $agency->update($request->validated());

        return new AgencyResource($agency);
    }

    public function destroy(Request $request, Agency $agency): Response
    {
        $agency->delete();

        return response()->noContent();
    }
}
