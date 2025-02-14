<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodAccountYearStoreRequest;
use App\Http\Requests\PeriodAccountYearUpdateRequest;
use App\Http\Resources\PeriodAccountYearCollection;
use App\Http\Resources\PeriodAccountYearResource;
use App\Models\PeriodAccountYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodAccountYearController extends Controller
{
    public function index(Request $request)
    {
        $periodAccountYears = PeriodAccountYear::all();

        return new PeriodAccountYearCollection($periodAccountYears);
    }

    public function store(PeriodAccountYearStoreRequest $request)
    {
        $periodAccountYear = PeriodAccountYear::create($request->validated());

        return new PeriodAccountYearResource($periodAccountYear);
    }

    public function show(Request $request, PeriodAccountYear $periodAccountYear)
    {
        return new PeriodAccountYearResource($periodAccountYear);
    }

    public function update(PeriodAccountYearUpdateRequest $request, PeriodAccountYear $periodAccountYear)
    {
        $periodAccountYear->update($request->validated());

        return new PeriodAccountYearResource($periodAccountYear);
    }

    public function destroy(Request $request, PeriodAccountYear $periodAccountYear)
    {
        $periodAccountYear->delete();

        return response()->noContent();
    }
}
