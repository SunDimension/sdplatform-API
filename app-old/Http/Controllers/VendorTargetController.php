<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorTargetStoreRequest;
use App\Http\Requests\VendorTargetUpdateRequest;
use App\Http\Resources\VendorTargetCollection;
use App\Http\Resources\VendorTargetResource;
use App\Models\VendorTarget;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorTargetController extends Controller
{
    public function index(Request $request): VendorTargetCollection
    {
        $vendortargets = VendorTarget::all();

        return new VendorTargetCollection($vendortargets);
    }

    public function store(VendorTargetStoreRequest $request): VendorTargetResource
    {
        $vendortargets = VendorTarget::create($request->validated());

        return new VendorTargetResource($vendortargets);
    }

    public function show(Request $request, VendorTarget $vendortargets): VendorTargetResource
    {
        return new VendorTargetResource($vendortargets);
    }

    public function update(VendorTargetUpdateRequest $request, VendorTarget $vendortargets): VendorTargetResource
    {
        $vendortargets->update($request->validated());

        return new VendorTargetResource($vendortargets);
    }

public function destroy($id)
    {   
       
        VendorTarget::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

