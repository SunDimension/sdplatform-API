<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodAccountDailyStoreRequest;
use App\Http\Requests\PeriodAccountDailyUpdateRequest;
use App\Http\Resources\PeriodAccountDailyCollection;
use App\Http\Resources\PeriodAccountDailyResource;
use App\Models\PeriodAccountDaily;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodAccountDailyController extends Controller
{
    public function index(Request $request)
    {
        $periodAccountDailies = PeriodAccountDaily::all();

        return new PeriodAccountDailyCollection($periodAccountDailies);
    }

    public function store(PeriodAccountDailyStoreRequest $request)
    {
        $periodAccountDaily = PeriodAccountDaily::create($request->validated());

        return new PeriodAccountDailyResource($periodAccountDaily);
    }

    public function show(Request $request, PeriodAccountDaily $periodAccountDaily)
    {
        return new PeriodAccountDailyResource($periodAccountDaily);
    }

    public function update(PeriodAccountDailyUpdateRequest $request, PeriodAccountDaily $periodAccountDaily)
    {
        $periodAccountDaily->update($request->validated());

        return new PeriodAccountDailyResource($periodAccountDaily);
    }

    public function destroy(Request $request, PeriodAccountDaily $periodAccountDaily)
    {
        $periodAccountDaily->delete();

        return response()->noContent();
    }
}
