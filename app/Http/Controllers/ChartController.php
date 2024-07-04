<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartStoreRequest;
use App\Http\Requests\ChartUpdateRequest;
use App\Http\Resources\ChartCollection;
use App\Http\Resources\ChartResource;
use App\Models\Chart;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartController extends Controller
{
    public function index(Request $request): Response
    {
        $charts = Chart::all();

        return new ChartCollection($charts);
    }

    public function store(ChartStoreRequest $request): Response
    {
        $chart = Chart::create($request->validated());

        return new ChartResource($chart);
    }

    public function show(Request $request, Chart $chart): Response
    {
        return new ChartResource($chart);
    }

    public function update(ChartUpdateRequest $request, Chart $chart): Response
    {
        $chart->update($request->validated());

        return new ChartResource($chart);
    }

    public function destroy(Request $request, Chart $chart): Response
    {
        $chart->delete();

        return response()->noContent();
    }
}
