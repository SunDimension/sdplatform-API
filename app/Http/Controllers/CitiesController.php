<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitiesStoreRequest;
use App\Http\Requests\CitiesUpdateRequest;
use App\Http\Resources\CityCollection;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CitiesController extends Controller
{
    public function index(Request $request): CityCollection
    {
        $cities = City::all();

        return new CityCollection($cities);
    }

    public function store(CitiesStoreRequest $request): CityResource
    {
        $city = City::create($request->validated());

        return new CityResource($city);
    }

    public function show(Request $request, City $city): CityResource
    {
        return new CityResource($city);
    }

    public function update(CitiesUpdateRequest $request, City $city): CityResource
    {
        $city->update($request->validated());

        return new CityResource($city);
    }

    public function destroy(Request $request, City $city): Response
    {
        $city->delete();

        return response()->noContent();
    }
}
