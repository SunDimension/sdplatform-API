<?php

namespace App\Http\Controllers;

use App\Http\Requests\InspectionBookingsStoreRequest;
use App\Http\Requests\InspectionBookingsUpdateRequest;
use App\Http\Resources\InspectionBookingCollection;
use App\Http\Resources\InspectionBookingResource;
use App\Models\InspectionBooking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InspectionBookingsController extends Controller
{
    public function index(Request $request): InspectionBookingCollection
    {
        $inspectionBookings = InspectionBooking::all();

        return new InspectionBookingCollection($inspectionBookings);
    }

    public function store(InspectionBookingsStoreRequest $request): InspectionBookingResource
    {
        $inspectionBooking = InspectionBooking::create($request->validated());

        return new InspectionBookingResource($inspectionBooking);
    }

    public function show(Request $request, InspectionBooking $inspectionBooking): InspectionBookingResource
    {
        return new InspectionBookingResource($inspectionBooking);
    }

    public function update(InspectionBookingsUpdateRequest $request, InspectionBooking $inspectionBooking): InspectionBookingResource
    {
        $inspectionBooking->update($request->validated());

        return new InspectionBookingResource($inspectionBooking);
    }

    public function destroy(Request $request, InspectionBooking $inspectionBooking): Response
    {
        $inspectionBooking->delete();

        return response()->noContent();
    }
}
