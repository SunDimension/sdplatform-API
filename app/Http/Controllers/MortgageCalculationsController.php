<?php

namespace App\Http\Controllers;

use App\Http\Requests\MortgageCalculationsStoreRequest;
use App\Http\Requests\MortgageCalculationsUpdateRequest;
use App\Http\Resources\MortgageCalculationCollection;
use App\Http\Resources\MortgageCalculationResource;
use App\Models\MortgageCalculation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MortgageCalculationsController extends Controller
{
    public function index(Request $request): MortgageCalculationCollection
    {
        $mortgageCalculations = MortgageCalculation::all();

        return new MortgageCalculationCollection($mortgageCalculations);
    }

    public function store(MortgageCalculationsStoreRequest $request): MortgageCalculationResource
    {
        $mortgageCalculation = MortgageCalculation::create($request->validated());

        return new MortgageCalculationResource($mortgageCalculation);
    }

    public function show(Request $request, MortgageCalculation $mortgageCalculation): MortgageCalculationResource
    {
        return new MortgageCalculationResource($mortgageCalculation);
    }

    public function update(MortgageCalculationsUpdateRequest $request, MortgageCalculation $mortgageCalculation): MortgageCalculationResource
    {
        $mortgageCalculation->update($request->validated());

        return new MortgageCalculationResource($mortgageCalculation);
    }

    public function destroy(Request $request, MortgageCalculation $mortgageCalculation): Response
    {
        $mortgageCalculation->delete();

        return response()->noContent();
    }
}
