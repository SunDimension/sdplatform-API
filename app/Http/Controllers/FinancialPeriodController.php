<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialPeriodStoreRequest;
use App\Http\Requests\FinancialPeriodUpdateRequest;
use App\Http\Resources\FinancialPeriodCollection;
use App\Http\Resources\FinancialPeriodResource;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FinancialPeriodController extends Controller
{
    public function index(Request $request): Response
    {
        $financialPeriods = FinancialPeriod::all();

        return new FinancialPeriodCollection($financialPeriods);
    }

    public function store(FinancialPeriodStoreRequest $request): Response
    {
        $financialPeriod = FinancialPeriod::create($request->validated());

        return new FinancialPeriodResource($financialPeriod);
    }

    public function show(Request $request, FinancialPeriod $financialPeriod): Response
    {
        return new FinancialPeriodResource($financialPeriod);
    }

    public function update(FinancialPeriodUpdateRequest $request, FinancialPeriod $financialPeriod): Response
    {
        $financialPeriod->update($request->validated());

        return new FinancialPeriodResource($financialPeriod);
    }

    public function destroy(Request $request, FinancialPeriod $financialPeriod): Response
    {
        $financialPeriod->delete();

        return response()->noContent();
    }
}
