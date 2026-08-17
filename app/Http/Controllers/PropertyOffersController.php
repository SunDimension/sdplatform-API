<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyOffersStoreRequest;
use App\Http\Requests\PropertyOffersUpdateRequest;
use App\Http\Resources\PropertyOfferCollection;
use App\Http\Resources\PropertyOfferResource;
use App\Models\PropertyOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyOffersController extends Controller
{
    public function index(Request $request): PropertyOfferCollection
    {
        $propertyOffers = PropertyOffer::all();

        return new PropertyOfferCollection($propertyOffers);
    }

    public function store(PropertyOffersStoreRequest $request): PropertyOfferResource
    {
        $propertyOffer = PropertyOffer::create($request->validated());

        return new PropertyOfferResource($propertyOffer);
    }

    public function show(Request $request, PropertyOffer $propertyOffer): PropertyOfferResource
    {
        return new PropertyOfferResource($propertyOffer);
    }

    public function update(PropertyOffersUpdateRequest $request, PropertyOffer $propertyOffer): PropertyOfferResource
    {
        $propertyOffer->update($request->validated());

        return new PropertyOfferResource($propertyOffer);
    }

    public function destroy(Request $request, PropertyOffer $propertyOffer): Response
    {
        $propertyOffer->delete();

        return response()->noContent();
    }
}
