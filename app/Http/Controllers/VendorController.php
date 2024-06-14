<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Http\Resources\VendorCollection;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorController extends Controller
{
    public function index(Request $request): VendorCollection
    {
        $vendors = Vendor::all();

        return new VendorCollection($vendors);
    }

    public function store(VendorStoreRequest $request): VendorResource
    {
        $vendor = Vendor::create($request->validated());

        return new VendorResource($vendor);
    }

    public function show(Request $request, Vendor $vendor): VendorResource
    {
        return new VendorResource($vendor);
    }

    public function update(VendorUpdateRequest $request, Vendor $vendor): VendorResource
    {
        $vendor->update($request->validated());

        return new VendorResource($vendor);
    }

    public function destroy(Request $request, Vendor $vendor): Response
    {
        $vendor->delete();

        return response()->noContent();
    }
}
