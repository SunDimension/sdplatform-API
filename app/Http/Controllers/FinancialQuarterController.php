<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialQuarterStoreRequest;
use App\Http\Requests\FinancialQuarterUpdateRequest;
use App\Http\Resources\FinancialQuarterCollection;
use App\Http\Resources\FinancialQuarterResource;
use App\Models\FinancialQuarter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FinancialQuarterController extends Controller
{
    public function index(Request $request)
    {
        $financialQuarters = FinancialQuarter::all();

        return new FinancialQuarterCollection($financialQuarters);
    }

    public function store(FinancialQuarterStoreRequest $request)
    {
        $financialQuarter = FinancialQuarter::create($request->validated());

        return new FinancialQuarterResource($financialQuarter);
    }

    public function show(Request $request, FinancialQuarter $financialQuarter)
    {
        return new FinancialQuarterResource($financialQuarter);
    }

    public function update(FinancialQuarterUpdateRequest $request, FinancialQuarter $financialQuarter)
    {
        $financialQuarter->update($request->validated());

        return new FinancialQuarterResource($financialQuarter);
    }

    public function destroy(Request $request, FinancialQuarter $financialQuarter)
    {
        $financialQuarter->delete();

        return response()->noContent();
    }
}
