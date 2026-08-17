<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportedListingsStoreRequest;
use App\Http\Requests\ReportedListingsUpdateRequest;
use App\Http\Resources\ReportedListingCollection;
use App\Http\Resources\ReportedListingResource;
use App\Models\ReportedListing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportedListingsController extends Controller
{
    public function index(Request $request): ReportedListingCollection
    {
        $reportedListings = ReportedListing::all();

        return new ReportedListingCollection($reportedListings);
    }

    public function store(ReportedListingsStoreRequest $request): ReportedListingResource
    {
        $reportedListing = ReportedListing::create($request->validated());

        return new ReportedListingResource($reportedListing);
    }

    public function show(Request $request, ReportedListing $reportedListing): ReportedListingResource
    {
        return new ReportedListingResource($reportedListing);
    }

    public function update(ReportedListingsUpdateRequest $request, ReportedListing $reportedListing): ReportedListingResource
    {
        $reportedListing->update($request->validated());

        return new ReportedListingResource($reportedListing);
    }

    public function destroy(Request $request, ReportedListing $reportedListing): Response
    {
        $reportedListing->delete();

        return response()->noContent();
    }
}
