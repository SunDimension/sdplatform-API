<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignationStoreRequest;
use App\Http\Requests\DesignationUpdateRequest;
use App\Http\Resources\DesignationCollection;
use App\Http\Resources\DesignationResource;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DesignationController extends Controller
{
    public function index(Request $request): DesignationCollection
    {
        $designations = Designation::all();

        return new DesignationCollection($designations);
    }

    public function store(DesignationStoreRequest $request): DesignationResource
    {
        $designation = Designation::create($request->validated());

        return new DesignationResource($designation);
    }

    public function show(Request $request, Designation $designation): DesignationResource
    {
        return new DesignationResource($designation);
    }

    public function update(DesignationUpdateRequest $request, Designation $designation): DesignationResource
    {
        $designation->update($request->validated());

        return new DesignationResource($designation);
    }

     public function destroy($id)
    {
       Designation::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
