<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartProviderStoreRequest;
use App\Http\Requests\ChartProviderUpdateRequest;
use App\Http\Resources\ChartProviderCollection;
use App\Http\Resources\ChartProviderResource;
use App\Models\ChartProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartProviderController extends Controller
{
    public function index(Request $request): Response
    {
        $chartProviders = ChartProvider::all();

        return new ChartProviderCollection($chartProviders);
    }

    public function store(ChartProviderStoreRequest $request): Response
    {
        $chartProvider = ChartProvider::create($request->validated());

        return new ChartProviderResource($chartProvider);
    }

    public function show(Request $request, ChartProvider $chartProvider): Response
    {
        return new ChartProviderResource($chartProvider);
    }

    public function update(ChartProviderUpdateRequest $request, ChartProvider $chartProvider): Response
    {
        $chartProvider->update($request->validated());

        return new ChartProviderResource($chartProvider);
    }

    public function destroy(Request $request, ChartProvider $chartProvider): Response
    {
        $chartProvider->delete();

        return response()->noContent();
    }
}
