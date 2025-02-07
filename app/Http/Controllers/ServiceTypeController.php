<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypeStoreRequest;
use App\Http\Requests\ServiceTypeUpdateRequest;
use App\Http\Resources\ServiceTypeCollection;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceTypeController extends Controller
{
    public function index(Request $request): ServiceTypeCollection
    {
        $servicetypes = ServiceType::all();

        return new ServiceTypeCollection($servicetypes);
    }

    public function store(ServiceTypeStoreRequest $request): ServiceTypeResource
    {
        $servicetypes = ServiceType::create($request->validated());

        return new ServiceTypeResource($servicetypes);
    }

    public function show(Request $request, ServiceType $servicetypes): ServiceTypeResource
    {
        return new ServiceTypeResource($servicetypes);
    }

    public function update(ServiceTypeUpdateRequest $request, ServiceType $servicetypes): ServiceTypeResource
    {
        $servicetypes->update($request->validated());

        return new ServiceTypeResource($servicetypes);
    }

public function destroy($id)
    {   
       
        ServiceType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

