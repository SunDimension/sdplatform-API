<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorCreditStoreRequest;
use App\Http\Requests\VendorCreditUpdateRequest;
use App\Http\Resources\VendorCreditCollection;
use App\Http\Resources\VendorCreditResource;
use App\Models\VendorCredit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorCreditController extends Controller
{
    public function index(Request $request): VendorCreditCollection
    {
        $vendorCredits = VendorCredit::all();

        return new VendorCreditCollection($vendorCredits);
    }

    public function store(VendorCreditStoreRequest $request): VendorCreditResource
    {
        $vendorCredit = VendorCredit::create($request->validated());

        return new VendorCreditResource($vendorCredit);
    }

    public function show(Request $request, VendorCredit $vendorCredit): VendorCreditResource
    {
        return new VendorCreditResource($vendorCredit);
    }

    public function update(VendorCreditUpdateRequest $request, VendorCredit $vendorCredit): VendorCreditResource
    {
        $vendorCredit->update($request->validated());

        return new VendorCreditResource($vendorCredit);
    }

    public function destroy(Request $request, VendorCredit $vendorCredit): Response
    {
        $vendorCredit->delete();

        return response()->noContent();
    }
}
