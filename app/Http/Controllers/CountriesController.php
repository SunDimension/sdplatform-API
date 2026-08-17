<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountriesStoreRequest;
use App\Http\Requests\CountriesUpdateRequest;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountriesController extends Controller
{
    public function index(Request $request): CountryCollection
    {
        $countries = Country::all();

        return new CountryCollection($countries);
    }

    public function store(CountriesStoreRequest $request): CountryResource
    {
        $country = Country::create($request->validated());

        return new CountryResource($country);
    }

    public function show(Request $request, Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    public function update(CountriesUpdateRequest $request, Country $country): CountryResource
    {
        $country->update($request->validated());

        return new CountryResource($country);
    }

    public function destroy(Request $request, Country $country): Response
    {
        $country->delete();

        return response()->noContent();
    }
}
