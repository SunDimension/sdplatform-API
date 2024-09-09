<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialYearStoreRequest;
use App\Http\Requests\FinancialYearUpdateRequest;
use App\Http\Resources\FinancialYearCollection;
use App\Http\Resources\FinancialYearResource;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FinancialYearController extends Controller
{
    public function index(Request $request)
    {
        $financialYears = FinancialYear::all();

        return new FinancialYearCollection($financialYears);
    }

    public function store(FinancialYearStoreRequest $request)
    {
        $financialYear = FinancialYear::create($request->validated());

        return new FinancialYearResource($financialYear);
    }

    public function show(Request $request, FinancialYear $financialYear)
    {
        return new FinancialYearResource($financialYear);
    }

    public function update(FinancialYearUpdateRequest $request, FinancialYear $financialYear)
    {
        $financialYear->update($request->validated());

        return new FinancialYearResource($financialYear);
    }

    public function destroy(Request $request, FinancialYear $financialYear)
    {
        $financialYear->delete();

        return response()->noContent();
    }
}
