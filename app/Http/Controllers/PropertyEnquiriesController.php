<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyEnquiriesStoreRequest;
use App\Http\Requests\PropertyEnquiriesUpdateRequest;
use App\Http\Resources\PropertyEnquiryCollection;
use App\Http\Resources\PropertyEnquiryResource;
use App\Models\PropertyEnquiry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyEnquiriesController extends Controller
{
    public function index(Request $request): PropertyEnquiryCollection
    {
        $propertyEnquiries = PropertyEnquiry::all();

        return new PropertyEnquiryCollection($propertyEnquiries);
    }

    public function store(PropertyEnquiriesStoreRequest $request): PropertyEnquiryResource
    {
        $propertyEnquiry = PropertyEnquiry::create($request->validated());

        return new PropertyEnquiryResource($propertyEnquiry);
    }

    public function show(Request $request, PropertyEnquiry $propertyEnquiry): PropertyEnquiryResource
    {
        return new PropertyEnquiryResource($propertyEnquiry);
    }

    public function update(PropertyEnquiriesUpdateRequest $request, PropertyEnquiry $propertyEnquiry): PropertyEnquiryResource
    {
        $propertyEnquiry->update($request->validated());

        return new PropertyEnquiryResource($propertyEnquiry);
    }

    public function destroy(Request $request, PropertyEnquiry $propertyEnquiry): Response
    {
        $propertyEnquiry->delete();

        return response()->noContent();
    }
}
