<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorTypeStoreRequest;
use App\Http\Requests\VendorTypeUpdateRequest;
use App\Http\Resources\VendorTypeCollection;
use App\Http\Resources\VendorTypeResource;
use App\Models\VendorType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorTypeController extends Controller
{
    public function index(Request $request): VendorTypeCollection
    {
        $vendorTypes = VendorType::all();

        return new VendorTypeCollection($vendorTypes);
    }

    public function store(VendorTypeStoreRequest $request): VendorTypeResource
    {
        $vendorType = VendorType::create($request->validated());

        return new VendorTypeResource($vendorType);
    }

    public function show(Request $request, VendorType $vendorType): VendorTypeResource
    {
        return new VendorTypeResource($vendorType);
    }

    public function update(VendorTypeUpdateRequest $request, VendorType $vendorType): VendorTypeResource
    {
        $vendorType->update($request->validated());

        return new VendorTypeResource($vendorType);
    }

    public function destroy(Request $request, VendorType $vendorType): Response
    {
        $vendorType->delete();

        return response()->noContent();
    }
}
