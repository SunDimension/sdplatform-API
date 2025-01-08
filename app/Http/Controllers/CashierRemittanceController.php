<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierRemittanceStoreRequest;
// use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\CashierRemittanceCollection;
use App\Http\Resources\CashierRemittanceResource;
use App\Models\CashierRemittance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashierRemittanceController extends Controller
{
    public function index(Request $request): CashierRemittanceCollection
    {
        $cashierExpenses = CashierRemittance::all();

        return new CashierRemittanceCollection($cashierExpenses);
    }

    public function store(CashierRemittanceStoreRequest $request): CashierRemittanceResource
    {
        $cashierExpenses = CashierRemittance::create($request->validated());

        return new CashierRemittanceResource($cashierExpenses);
    }

    public function show(Request $request, CashierRemittance $cashierExpenses): CashierRemittanceResource
    {
        return new CashierRemittanceResource($cashierExpenses);
    }

    // public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    // {
    //     $branch->update($request->validated());

    //     return new BranchResource($branch);
    // }

    public function destroy($id)
    {
        // $branch->delete();
        CashierRemittance::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
